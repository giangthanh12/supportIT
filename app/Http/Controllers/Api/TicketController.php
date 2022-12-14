<?php

namespace App\Http\Controllers\Api;

use App\bitrix\CRest;
use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Http\Requests\ShortcutRequest;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\updateAssigneeRequest;
use App\Jobs\NotifyBitrix;
use App\Models\Comment;
use App\Models\Group;
use App\Models\History;
use App\Models\Ticket;
use App\Models\User;
use App\Notify\NotifyBitrix\NotifyBitrix24;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use PHPUnit\TextUI\Help;
use App\Traits\HistoryTrait;
use App\Traits\ImageUploadTrait;
use App\Traits\ResponseTrait;

class TicketController extends Controller
{
    use HistoryTrait;
    use ResponseTrait;
    use ImageUploadTrait;
    public function getGroup() {
        $groups = Group::select("id", "group_name as text")->get();
        return $groups;
    }
    public function getAssignee() {
        $groups = User::select("id", "name", "avatar")->get();
        return $groups;
    }
    public function getAssigneeByGroup($group_id) {
        $group = Group::select("members_id as members")->where("id", $group_id)->first();
        return $group->members;
    }
    public function getMembers(Request $request) {
        if($request->has("id") && !empty($request->id)) {

            $array_id = strpos($request->id,",") ? explode(",", $request->id) : [$request->id];
            $userCallback= CRest::call('user.get',[ "id"=>$array_id ]);
        }
        else {
            $userCallback= CRest::call('user.search',["name"=>$request->has("keyWord") && !empty($request->keyWord) ? $request->keyWord : ""]);
        }
        foreach($userCallback["result"] as $userBitrix) {
            $data[] = ["id"=>$userBitrix["ID"], 'email'=>$userBitrix["EMAIL"], "text"=>$userBitrix["LAST_NAME"]." ".$userBitrix["SECOND_NAME"]." ".$userBitrix["NAME"]];
        }
        return $data;
    }
    public function getAssigneeByTicket($ticket_id) {
        $ticket = Ticket::find($ticket_id);
        return !empty($ticket->assignees_id) && !is_null($ticket->assignees_id) ? $ticket->assignees_id : [];
    }
    public function getAssigneeGroup(Request $request) {
        $group = Group::where('leader_id', $request->auth_id)->select("members_id as members")->first();
        return $group->members;
    }
    public function get_my_ticket(Request $request) {
        $status = $request->has("status") ? $request->get("status") : "";
        return view("api.my-ticket", compact("status"));
    }
    public function get_assign_ticket(Request $request) {
        $status = $request->has("status") ? $request->get("status") : "";
        return view("api.assign-ticket", compact("status"));
    }
    public function save(TicketRequest $request) {

        $file_path = null;
        if(!is_null($request->file('ticket_file')))
            $file_path = $this->upload_file($request->file('ticket_file'));
        $ticket = Ticket::create([
            "title"=>$request->input("ticket-title"),
            "creator_id"=>Auth::id(),
            "name_creator"=>$request->name_creator,
            "deadline"=>$request->input("ticket-deadline"),
            "cc"=>$request->input("cc"),
            "email_creator"=>$request->email_creator,
            "group_id"=>$request->group_id,
            "file"=>$file_path,
            "assignees_id"=>$request->input("task-assigned"),
            "content"=>$request->content,
            "level"=>$request->input("ticket-level"),
        ]);
            // update history
            $this->addHistory($ticket->id, "T???o y??u c???u");
            $attribute = [
                "message"=>auth()->user()->name." v???a t???o m???i m???t y??u c???u [b]$ticket->title[/b]",
                "group_name"=>$ticket->group->group_name,
                "deadline"=>Carbon::parse($ticket->deadline)->format("d.m.Y H:i")
            ];
            if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                (new NotifyBitrix24())->sendOneMember($ticket,auth()->user()->storeToken,$request->input("cc"),$attribute);
            }
        // notify t???t c??? c??c th??nh vi??n trong nh??m c?? y??u c???u m???i
        (new NotifyBitrix24())->sendAllMembers($ticket,auth()->user()->storeToken, $attribute);
        if(!Helper::checkTime(Carbon::parse($request->input('ticket-deadline')))) {
            $message = "L???ch deadline kh??ng n???m trong l???ch l??m vi???c nh??n vi??n. Vui l??ng ch???nh s???a l???i deadline cho ph?? h???p!";
            return $this->warningResponse($ticket, $message,201);
        }
        return $this->successResponse($ticket,"T???o y??u c???u th??nh c??ng",201);
    }
    // get ticket by creator
    public function getdata(Request $request) {
        $tickets = Ticket::with("user:id,name,avatar")->select("id", "title", "level","status","creator_id","created_at")
        ->OwnTicket()
        ->ConditionLevel($request)
        ->ConditionStatus($request)
        ->ConditionTime($request)
        ->ConditionGroup($request)
        ->orderBy("status", "ASC")
        ->orderBy("level", "ASC")
        ->orderBy("created_at", "DESC")->get();
        return $tickets;
    }
    // get ticket by assignee
    public function getdata_assign_ticket(Request $request) {
        $auth_id = '"'.Auth::id().'"';
        $groups_id = Group::where("members_id", "like",'%'.$auth_id.'%')->pluck("id");
        $tickets = Ticket::with("user:id,name,avatar")->select("id", "title", "level","assignees_id","status","creator_id","created_at")
        ->whereIn('group_id', $groups_id)
        ->ConditionLevel($request)
        ->ConditionStatus($request)
        ->ConditionTime($request)
        ->orderBy("status", "ASC")
        ->orderBy("level", "ASC")
        ->orderBy("created_at", "DESC")->get();
        return $tickets;
    }

    public function get_ticket_incomplete(Request $request) {
        if(!Helper::checkLeader($request->auth_id)) return false;
        $group = Group::where('leader_id',$request->auth_id)->first();
        $tickets = Ticket::select("id", "title as text")->where("group_id", $group->id)->whereIn("status", [1,2])->get();
        return $tickets;
    }

    public function detail($id) {
        $ticket = Ticket::find($id);
        if(!$ticket)
        return back();
        $leader_id = Group::find($ticket->group_id)->leader_id;
        return view("api.detail-ticket",compact("ticket", "leader_id"));
    }
    public function getHistories($ticket_id) {
        $histories = History::with("user:id,name")->where("ticket_id", $ticket_id)->latest()->get();
        return $this->successResponse($histories,"L???y d??? li???u th??nh c??ng",200);
    }
    public function getComments($id_ticket) {
        $comments = Comment::select("id","content", "count_like", "sender_id", "ticket_id", "updated_at as time_ago")->where("ticket_id", $id_ticket)->with("user:id,name,avatar")->get();
        return $this->successResponse($comments,"L???y d??? li???u th??nh c??ng",200);
    }

    public function update(TicketRequest $request, $id) {
        $ticket = Ticket::find($id);
        if($ticket->creator_id != Auth::id()) {
            return $this->errorResponse("B???n kh??ng c?? quy???n s???a y??u c???u",Response::HTTP_UNAUTHORIZED);
        }
        $file_path = !empty($request->ticket_file_old) ? $request->ticket_file_old : null;
        if(!is_null($request->file('ticket_file')))
        $file_path = $this->upload_file($request->file('ticket_file'));
        if(strtotime($request->input("ticket-deadline")) > strtotime($ticket->deadline))
        $ticket->confirm_deadline = NULL;
        $ticket->title=$request->input("ticket-title");
        $ticket->creator_id=Auth::id();
        $ticket->deadline = $request->input("ticket-deadline");
        $ticket->cc = $request->input("cc");
        $ticket->name_creator=$request->name_creator;
        $ticket->email_creator=$request->email_creator;
        $ticket->group_id=$request->group_id;
        $ticket->file=$file_path;
        $ticket->assignees_id = $request->input("task-assigned");
        $ticket->content=$request->content;
        $ticket->level=$request->input("ticket-level");
        $ticket->save();
        // update histories
        $this->addHistory($ticket->id, "???? c???p nh???t l???i y??u c???u");
        // Chia l??m 2 tr?????ng h???p
        $NotifyBitrix24 = new NotifyBitrix24();
        $attribute = [
            "message"=>auth()->user()->name." v???a c???p nh???t y??u c???u.",
            "group_name"=>"",
            "deadline"=>""
        ];
        if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
            (new NotifyBitrix24())->sendOneMember($ticket,auth()->user()->storeToken,$request->input("cc"),$attribute);
        }
        if($ticket->status == 1) {//n???u y??u c???u ??ang ch??? x??? l?? th?? g???i th??ng b??o cho t???t c??? th??nh vi??n trong nh??m
            $NotifyBitrix24->sendAllMembers($ticket,auth()->user()->storeToken, $attribute);
        }
        else {// N???u y??u c???u ??ang x??? l?? th?? ch??? notify nh???ng ng?????i li??n quan v?? nh??m tr?????ng
            $NotifyBitrix24->sendLeaderAndRelatedMember($ticket, auth()->user()->storeToken, $attribute);
        }
        return $this->successResponse($ticket,"C???p nh???t y??u c???u th??nh c??ng",200);
    }
    public function confirm_ticket(Request $request) {
        $ticket = Ticket::find($request->ticket_id);
        $ticket->status = $request->status_ticket;
        $attribute = [
            "message"=>"",
            "group_name"=>"",
            "deadline"=>""
        ];
        $ticket->save();
        if($request->type == "switch") {
            $desc = $request->status_ticket == 2 ?"Tr???ng th??i y??u c???u thay ?????i t??? ????ng y??u c???u <i class='fas fa-arrow-right'></i> ??ang x??? l??" : "Tr???ng th??i y??u c???u thay ?????i t??? ??ang x??? l?? <i class='fas fa-arrow-right'></i> ????ng y??u c???u";
            $attribute["message"] = $request->status_ticket == 2 ? auth()->user()->name." ???? thay ?????i tr???ng th??i y??u c???u t??? ????ng y??u c???u sang ??ang x??? l??" : auth()->user()->name." ???? thay ?????i y??u c???u t??? ??ang x??? l?? sang ????ng y??u c???u";
        }
        else {
            $desc = $request->status_ticket == 2 ?"Tr???ng th??i y??u c???u thay ?????i t??? ???? x??? l?? <i class='fas fa-arrow-right'></i> ??ang x??? l??" : "Tr???ng th??i y??u c???u thay ?????i t??? ???? x??? l?? <i class='fas fa-arrow-right'></i> ????ng y??u c???u";
            $attribute["message"] = $request->status_ticket == 2 ? auth()->user()->name." ???? thay ?????i tr???ng th??i y??u c???u t??? ???? x??? l?? sang ??ang x??? l??" : auth()->user()->name." ???? thay ?????i tr???ng th??i y??u c???u t??? ???? x??? l?? sang ????ng y??u c???u";
        }
        $this->addHistory($ticket->id, $desc);
        // notify
        $NotifyBitrix24 = new NotifyBitrix24();
        if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
           $NotifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc,$attribute);
        }
        // $NotifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->creator_id,$attribute);
        $NotifyBitrix24->sendLeaderAndRelatedMember($ticket, auth()->user()->storeToken, $attribute);
        $msg = $request->status_ticket == 2 ?"???? h???y ????ng y??u c???u" : "???? x??c nh???n ????ng y??u c???u";
        return $this->successResponse($ticket,$msg,200);
      }


        // b???n c??
        public function update_assignee(Request $request) {// Ti???p nh???n y??u c???u
            $ticket = Ticket::find($request->id_ticket);
            $leader_id = Group::where("members_id", "like",'%'.'"'.Auth::id().'"'.'%')->first()->leader_id;
            $notifyBitrix24 = new NotifyBitrix24();
            $assignees_id = $ticket->assignees_id;
            $attribute = [
                "message"=>"",
                "group_name"=>"",
                "deadline"=>""
            ];
            if(is_null($assignees_id)  || empty($assignees_id)) {
                $ticket->assignees_id = json_encode([(string) Auth::id()]);
                $ticket->status = $ticket->status == 1 ? 2 : $ticket->status;
                $this->addHistory($request->id_ticket, "Tr???ng th??i y??u c???u thay ?????i t??? ??ang ch??? x??? l??<i class='fas fa-arrow-right'></i> ??ang x??? l??");
                  if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                    $attribute["message"] = auth()->user()->name. " ???? ti???p nh???n y??u c???u";
                    $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc,$attribute);
                 }
                $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, $attribute);
                $attribute["message"] = auth()->user()->name. " trong nh??m c???a b???n ???? ti???p nh???n y??u c???u";
                $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id, $attribute); // th??ng b??o cho leader
            } else {
                $assignees_id = json_decode($assignees_id);
                if(in_array((string) Auth::id(), $assignees_id)) {
                    $key = array_search((string) Auth::id(), $assignees_id);
                    if($ticket->status == 1) {
                        $ticket->status = 2;
                        $this->addHistory($request->id_ticket, "Tr???ng th??i y??u c???u thay ?????i t??? ??ang ch??? x??? l?? <i class='fas fa-arrow-right'></i> ??ang x??? l??");
                        $attribute["message"] = auth()->user()->name. " ???? ti???p nh???n y??u c???u";
                        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, $attribute);
                          if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                            $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc, $attribute);
                        }
                        if(Auth::id() != $leader_id) {
                            $attribute["message"] = auth()->user()->name. " trong nh??m c???a b???n ???? ti???p nh???n y??u c???u";
                            $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id, $attribute); // th??ng b??o cho leader
                        }
                    }
                    else {
                        array_splice($assignees_id, $key, 1);
                        if(empty($assignees_id)) {
                            $ticket->status = 1;
                            $this->addHistory($request->id_ticket, "Tr???ng th??i y??u c???u thay ?????i t??? ??ang x??? l??<i class='fas fa-arrow-right'></i> ??ang ch??? x??? l??");
                        }
                        $attribute["message"] = auth()->user()->name. " ???? h???y ti???p nh???n y??u c???u";
                          if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                            $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc,$attribute);
                        }
                        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id,$attribute);
                        if(Auth::id() != $leader_id)
                        {
                            $attribute["message"] = auth()->user()->name. " trong nh??m c???a b???n ???? h???y ti???p nh???n y??u c???u";
                            $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id, $attribute); // th??ng b??o cho leader
                        }
                    }
                }
                else {
                    $assignees_id[] = (string) Auth::id();
                    $attribute["message"] = auth()->user()->name. " ???? ti???p nh???n y??u c???u";
                    $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id,$attribute); // th??ng b??o cho ng?????i t???o y??u c???u
                      if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                        $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc,$attribute);
                    }
                    if(Auth::id() != $leader_id) {
                        $attribute["message"] = auth()->user()->name. " trong nh??m c???a b???n ???? ti???p nh???n y??u c???u";
                        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id,$attribute); // th??ng b??o cho leader
                    }
                    $ticket->status = 2;
                }
                $ticket->assignees_id = json_encode($assignees_id);
            }
            $ticket->save();
            return $this->successResponse($ticket,"C???p nh???t th??nh c??ng",200);
        }
    public function check_permisiion_assign(Request $request) {
        $result = false;
        if(Helper::checkLeader(Auth::id()))
        $result = true;
        return $this->successResponse($result,"C???p nh???t th??nh c??ng",200);
    }
    public function update_assignee_ticket(updateAssigneeRequest $request) {
        if(!Helper::checkLeader(Auth::id())) return false;
        $ticket_id = $request->get("ticket-title");
        $ticket = Ticket::find($ticket_id);
        $ticket->assignees_id = $request->get("task-assigned");
        $status_before = $ticket->status;
        $ticket->status = 2;
        $ticket->save();
        if($status_before == 1)
        $this->addHistory($request->get("ticket-title"),"Tr???ng th??i y??u c???u thay ?????i t??? ??ang ch??? x??? l??<i class='fas fa-arrow-right'></i> ??ang x??? l??");
        // notify
        $attribute = [
            "message"=>"",
            "group_name"=>"",
            "deadline"=>""
        ];
        $notifyBitrix24 = new NotifyBitrix24();
        $desc = "";
        foreach($request->get("task-assigned") as $id_assignee) {
            $user = User::find($id_assignee);
            $desc .= $user->name. " ";
        }
        $attribute["message"] = auth()->user()->name. " ???? ph??n c??ng y??u c???u cho b???n";
        $notifyBitrix24->sendMembers($ticket, auth()->user()->storeToken, $ticket->assignees_id, $attribute);
        $attribute["message"] = auth()->user()->name. " ???? ph??n c??ng y??u c???u cho ".$desc. " x??? l?? y??u c???u c???a b???n";
        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, $attribute);
        if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
            $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc, $attribute);
        }
        return $this->successResponse([],"Ph??n c??ng c??ng vi???c th??nh c??ng!",200);
    }
    public function delete($id_ticket) {
        $ticket = Ticket::find($id_ticket);
        if($ticket->creator_id != Auth::id())
        return $this->errorResponse([],"B???n kh??ng c?? quy???n x??a y??u c???u!",401);
        $ticket->delete();
        return $this->successResponse([],"???? x??a y??u c???u th??nh c??ng!",200);
    }
    public function comment(Request $request) {
        $comment = Comment::create([
            "sender_id"=>Auth::id(),
            "ticket_id"=>$request->ticket_id_comment,
            "content"=>$request->content_comment
        ]);
        $notifyBitrix24 = new NotifyBitrix24();
        $ticket = Ticket::find($request->ticket_id_comment);
        // send notify
        $attribute = [
            "message"=>auth()->user()->name. " v???a b??nh lu???n y??u c???u.",
            "group_name"=>"",
            "deadline"=>""
        ];
        if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
            $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc, $attribute);
        }
        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, $attribute); // g???i cho ng?????i t???o
        $notifyBitrix24->sendLeaderAndRelatedMember($ticket, auth()->user()->storeToken, $attribute); // g???i cho t???t c??? thanh vi??n li??n quan v?? leader
        $dataResponse = [];
        $dataResponse['comment_id'] = $comment->id;
        $dataResponse['content'] = $comment->content;
        $dataResponse['time_ago'] = $comment->created_at->diffForHumans();
        return $this->successResponse($dataResponse,"B??nh lu???n th??nh c??ng!",201);
    }
    public function update_success($id_ticket) { // ????ng t??c v??? or m??? t??c v???
        $ticket = Ticket::find($id_ticket);
        $ticket->status = $ticket->status != 3 ? 3 : 2;
        $ticket->save();
         //update history ticket
         $this->addHistory($id_ticket, $ticket->status != 3 ? "Tr???ng th??i y??u c???u thay ?????i t??? ???? x??? l?? <i class='fas fa-arrow-right'></i> ??ang x??? l??" : "Tr???ng th??i y??u c???u thay ?????i t??? ??ang x??? l?? <i class='fas fa-arrow-right'></i> ???? x??? l??");
         $attribute = [
            "message"=>"",
            "group_name"=>"",
            "deadline"=>""
        ];
        $notifyBitrix24 = new NotifyBitrix24();
        $attribute["message"] = $ticket->status == 3 ? auth()->user()->name. " ???? x??c nh???n ho??n th??nh y??u c???u." : auth()->user()->name. " ???? chuy???n tr???ng th??i y??u c???u ??ang x??? l??.";
        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, $attribute);
        if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
            $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc, $attribute);
        }
        $msg = $ticket->status == 3 ? "???? chuy???n tr???ng th??i ???? x??? l?? cho y??u c???u." : "???? chuy???n tr???ng ??ang x??? l?? cho y??u c???u.";
        return $this->successResponse([],$msg,200);
    }
    public function like_comment(Request $request) {
        $comment = Comment::find($request->comment_id);
        if($comment->users->contains(Auth::id())) {
            $comment->users()->detach(Auth::id());
            $comment->count_like = $comment->count_like-1;
        }
        else {
            $comment->users()->attach(Auth::id());
            $comment->count_like = $comment->count_like+1;
        }
        $comment->save();
        // notify comment to user
        if(auth()->user()->id != $comment->sender_id) {
            $ticket = Ticket::find($comment->ticket_id);
            $attribute = [
                "message"=>auth()->user()->name. " v???a m???i th??ch b??nh lu???n c???a b???n",
                "group_name"=>"",
                "deadline"=>""
            ];
            (new NotifyBitrix24())->sendOneMember($ticket,auth()->user()->storeToken, $comment->sender_id, $attribute);
        }
        return $this->successResponse([],"???? c???p nh???t tr???ng th??i comment",200);
    }
    public function update_comment(Request $request) {
        $comment = Comment::find($request->comment_id);
        $comment->content = $request->content_comment;
        $comment->save();
        return $this->successResponse([],"???? c???p nh???t tr???ng th??i comment!",200);
    }
    public function delete_comment(Request $request) {
        $comment = Comment::find($request->comment_id);
        $comment->delete();
        return $this->successResponse([],"???? x??a comment th??nh c??ng!",200);
    }
    public function save_shortcut(ShortcutRequest $request) {
        $file_path = null;
        if(!is_null($request->file('ticket_file_shortcut')))
            $file_path = $this->upload_file($request->file('ticket_file_shortcut'));
        $ticket = Ticket::create([
            "title"=>$request->input("ticket-title_shortcut"),
            "creator_id"=>Auth::id(),
            "name_creator"=>$request->name_creator_shortcut,
            "deadline"=>Carbon::parse($request->input("ticket-deadline_shortcut"))->format("Y-m-d H:i"),
            "cc"=>!is_null($request->input("cc_shortcut")) ? $request->input("cc_shortcut") : NULL,
            "email_creator"=>$request->email_creator_shortcut,
            "group_id"=>$request->group_id_shortcut,
            "file"=>$file_path,
            "assignees_id"=>is_null($request->input("task-assigned_shortcut")) ? NULL : json_encode($request->input("task-assigned_shortcut")),
            "content"=>$request->content_shortcut,
            "level"=>$request->input("ticket-level_shortcut"),
        ]);
            // update history
            $this->addHistory($ticket->id, "T???o y??u c???u");
            $attribute = [
                "message"=>auth()->user()->name." v???a t???o m???i m???t y??u c???u [b]$ticket->title[/b]",
                "group_name"=>$ticket->group->group_name,
                "deadline"=>Carbon::parse($ticket->deadline)->format("d.m.Y H:i")
            ];
            if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                (new NotifyBitrix24())->sendOneMember($ticket,auth()->user()->storeToken,$request->input("cc_shortcut"),$attribute);
            }
        // notify t???t c??? c??c th??nh vi??n trong nh??m c?? y??u c???u m???i
        (new NotifyBitrix24())->sendAllMembers($ticket,auth()->user()->storeToken, $attribute);
        if(!Helper::checkTime(Carbon::parse($request->input('ticket-deadline_shortcut')))) {
            $message = "L???ch deadline kh??ng n???m trong l???ch l??m vi???c nh??n vi??n. Vui l??ng ch???nh s???a l???i deadline cho ph?? h???p!";
            return $this->warningResponse($ticket, $message,201);
        }
        return $this->successResponse($ticket,"T???o y??u c???u th??nh c??ng",201);
    }
}
