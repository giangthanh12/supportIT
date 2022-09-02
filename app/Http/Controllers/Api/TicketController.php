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
            $this->addHistory($ticket->id, "Tạo yêu cầu");
            $attribute = [
                "message"=>auth()->user()->name." vừa tạo mới một yêu cầu [b]$ticket->title[/b]",
                "group_name"=>$ticket->group->group_name,
                "deadline"=>Carbon::parse($ticket->deadline)->format("d.m.Y H:i")
            ];
            if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                (new NotifyBitrix24())->sendOneMember($ticket,auth()->user()->storeToken,$request->input("cc"),$attribute);
            }
        // notify tất cả các thành viên trong nhóm có yêu cầu mới
        (new NotifyBitrix24())->sendAllMembers($ticket,auth()->user()->storeToken, $attribute);
        if(!Helper::checkTime(Carbon::parse($request->input('ticket-deadline')))) {
            $message = "Lịch deadline không nằm trong lịch làm việc nhân viên. Vui lòng chỉnh sửa lại deadline cho phù hợp!";
            return $this->warningResponse($ticket, $message,201);
        }
        return $this->successResponse($ticket,"Tạo yêu cầu thành công",201);
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
        return $this->successResponse($histories,"Lấy dữ liệu thành công",200);
    }
    public function getComments($id_ticket) {
        $comments = Comment::select("id","content", "count_like", "sender_id", "ticket_id", "updated_at as time_ago")->where("ticket_id", $id_ticket)->with("user:id,name,avatar")->get();
        return $this->successResponse($comments,"Lấy dữ liệu thành công",200);
    }

    public function update(TicketRequest $request, $id) {
        $ticket = Ticket::find($id);
        if($ticket->creator_id != Auth::id()) {
            return $this->errorResponse("Bạn không có quyền sửa yêu cầu",Response::HTTP_UNAUTHORIZED);
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
        $this->addHistory($ticket->id, "Đã cập nhật lại yêu cầu");
        // Chia làm 2 trường hợp
        $NotifyBitrix24 = new NotifyBitrix24();
        $attribute = [
            "message"=>auth()->user()->name." vừa cập nhật yêu cầu.",
            "group_name"=>"",
            "deadline"=>""
        ];
        if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
            (new NotifyBitrix24())->sendOneMember($ticket,auth()->user()->storeToken,$request->input("cc"),$attribute);
        }
        if($ticket->status == 1) {//nếu yêu cầu đang chờ xử lý thì gửi thông báo cho tất cả thành viên trong nhóm
            $NotifyBitrix24->sendAllMembers($ticket,auth()->user()->storeToken, $attribute);
        }
        else {// Nếu yêu cầu đang xử lý thì chỉ notify những người liên quan và nhóm trưởng
            $NotifyBitrix24->sendLeaderAndRelatedMember($ticket, auth()->user()->storeToken, $attribute);
        }
        return $this->successResponse($ticket,"Cập nhật yêu cầu thành công",200);
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
            $desc = $request->status_ticket == 2 ?"Trạng thái yêu cầu thay đổi từ đóng yêu cầu <i class='fas fa-arrow-right'></i> đang xử lý" : "Trạng thái yêu cầu thay đổi từ đang xử lý <i class='fas fa-arrow-right'></i> đóng yêu cầu";
            $attribute["message"] = $request->status_ticket == 2 ? auth()->user()->name." đã thay đổi trạng thái yêu cầu từ đóng yêu cầu sang đang xử lý" : auth()->user()->name." đã thay đổi yêu cầu từ đang xử lý sang đóng yêu cầu";
        }
        else {
            $desc = $request->status_ticket == 2 ?"Trạng thái yêu cầu thay đổi từ đã xử lý <i class='fas fa-arrow-right'></i> đang xử lý" : "Trạng thái yêu cầu thay đổi từ đã xử lý <i class='fas fa-arrow-right'></i> đóng yêu cầu";
            $attribute["message"] = $request->status_ticket == 2 ? auth()->user()->name." đã thay đổi trạng thái yêu cầu từ đã xử lý sang đang xử lý" : auth()->user()->name." đã thay đổi trạng thái yêu cầu từ đã xử lý sang đóng yêu cầu";
        }
        $this->addHistory($ticket->id, $desc);
        // notify
        $NotifyBitrix24 = new NotifyBitrix24();
        if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
           $NotifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc,$attribute);
        }
        // $NotifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->creator_id,$attribute);
        $NotifyBitrix24->sendLeaderAndRelatedMember($ticket, auth()->user()->storeToken, $attribute);
        $msg = $request->status_ticket == 2 ?"Đã hủy đóng yêu cầu" : "Đã xác nhận đóng yêu cầu";
        return $this->successResponse($ticket,$msg,200);
      }


        // bản cũ
        public function update_assignee(Request $request) {// Tiếp nhận yêu cầu
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
                $this->addHistory($request->id_ticket, "Trạng thái yêu cầu thay đổi từ đang chờ xử lý<i class='fas fa-arrow-right'></i> đang xử lý");
                  if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                    $attribute["message"] = auth()->user()->name. " đã tiếp nhận yêu cầu";
                    $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc,$attribute);
                 }
                $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, $attribute);
                $attribute["message"] = auth()->user()->name. " trong nhóm của bạn đã tiếp nhận yêu cầu";
                $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id, $attribute); // thông báo cho leader
            } else {
                $assignees_id = json_decode($assignees_id);
                if(in_array((string) Auth::id(), $assignees_id)) {
                    $key = array_search((string) Auth::id(), $assignees_id);
                    if($ticket->status == 1) {
                        $ticket->status = 2;
                        $this->addHistory($request->id_ticket, "Trạng thái yêu cầu thay đổi từ đang chờ xử lý <i class='fas fa-arrow-right'></i> đang xử lý");
                        $attribute["message"] = auth()->user()->name. " đã tiếp nhận yêu cầu";
                        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, $attribute);
                          if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                            $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc, $attribute);
                        }
                        if(Auth::id() != $leader_id) {
                            $attribute["message"] = auth()->user()->name. " trong nhóm của bạn đã tiếp nhận yêu cầu";
                            $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id, $attribute); // thông báo cho leader
                        }
                    }
                    else {
                        array_splice($assignees_id, $key, 1);
                        if(empty($assignees_id)) {
                            $ticket->status = 1;
                            $this->addHistory($request->id_ticket, "Trạng thái yêu cầu thay đổi từ đang xử lý<i class='fas fa-arrow-right'></i> đang chờ xử lý");
                        }
                        $attribute["message"] = auth()->user()->name. " đã hủy tiếp nhận yêu cầu";
                          if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                            $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc,$attribute);
                        }
                        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id,$attribute);
                        if(Auth::id() != $leader_id)
                        {
                            $attribute["message"] = auth()->user()->name. " trong nhóm của bạn đã hủy tiếp nhận yêu cầu";
                            $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id, $attribute); // thông báo cho leader
                        }
                    }
                }
                else {
                    $assignees_id[] = (string) Auth::id();
                    $attribute["message"] = auth()->user()->name. " đã tiếp nhận yêu cầu";
                    $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id,$attribute); // thông báo cho người tạo yêu cầu
                      if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                        $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc,$attribute);
                    }
                    if(Auth::id() != $leader_id) {
                        $attribute["message"] = auth()->user()->name. " trong nhóm của bạn đã tiếp nhận yêu cầu";
                        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id,$attribute); // thông báo cho leader
                    }
                    $ticket->status = 2;
                }
                $ticket->assignees_id = json_encode($assignees_id);
            }
            $ticket->save();
            return $this->successResponse($ticket,"Cập nhật thành công",200);
        }
    public function check_permisiion_assign(Request $request) {
        $result = false;
        if(Helper::checkLeader(Auth::id()))
        $result = true;
        return $this->successResponse($result,"Cập nhật thành công",200);
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
        $this->addHistory($request->get("ticket-title"),"Trạng thái yêu cầu thay đổi từ đang chờ xử lý<i class='fas fa-arrow-right'></i> đang xử lý");
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
        $attribute["message"] = auth()->user()->name. " đã phân công yêu cầu cho bạn";
        $notifyBitrix24->sendMembers($ticket, auth()->user()->storeToken, $ticket->assignees_id, $attribute);
        $attribute["message"] = auth()->user()->name. " đã phân công yêu cầu cho ".$desc. " xử lý yêu cầu của bạn";
        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, $attribute);
        if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
            $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc, $attribute);
        }
        return $this->successResponse([],"Phân công công việc thành công!",200);
    }
    public function delete($id_ticket) {
        $ticket = Ticket::find($id_ticket);
        if($ticket->creator_id != Auth::id())
        return $this->errorResponse([],"Bạn không có quyền xóa yêu cầu!",401);
        $ticket->delete();
        return $this->successResponse([],"Đã xóa yêu cầu thành công!",200);
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
            "message"=>auth()->user()->name. " vừa bình luận yêu cầu.",
            "group_name"=>"",
            "deadline"=>""
        ];
        if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
            $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc, $attribute);
        }
        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, $attribute); // gửi cho người tạo
        $notifyBitrix24->sendLeaderAndRelatedMember($ticket, auth()->user()->storeToken, $attribute); // gửi cho tất cả thanh viên liên quan và leader
        $dataResponse = [];
        $dataResponse['comment_id'] = $comment->id;
        $dataResponse['content'] = $comment->content;
        $dataResponse['time_ago'] = $comment->created_at->diffForHumans();
        return $this->successResponse($dataResponse,"Bình luận thành công!",201);
    }
    public function update_success($id_ticket) { // đóng tác vụ or mở tác vụ
        $ticket = Ticket::find($id_ticket);
        $ticket->status = $ticket->status != 3 ? 3 : 2;
        $ticket->save();
         //update history ticket
         $this->addHistory($id_ticket, $ticket->status != 3 ? "Trạng thái yêu cầu thay đổi từ đã xử lý <i class='fas fa-arrow-right'></i> đang xử lý" : "Trạng thái yêu cầu thay đổi từ đang xử lý <i class='fas fa-arrow-right'></i> đã xử lý");
         $attribute = [
            "message"=>"",
            "group_name"=>"",
            "deadline"=>""
        ];
        $notifyBitrix24 = new NotifyBitrix24();
        $attribute["message"] = $ticket->status == 3 ? auth()->user()->name. " đã xác nhận hoàn thành yêu cầu." : auth()->user()->name. " đã chuyển trạng thái yêu cầu đang xử lý.";
        $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, $attribute);
        if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
            $notifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->cc, $attribute);
        }
        $msg = $ticket->status == 3 ? "Đã chuyển trạng thái đã xử lý cho yêu cầu." : "Đã chuyển trạng đang xử lý cho yêu cầu.";
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
                "message"=>auth()->user()->name. " vừa mới thích bình luận của bạn",
                "group_name"=>"",
                "deadline"=>""
            ];
            (new NotifyBitrix24())->sendOneMember($ticket,auth()->user()->storeToken, $comment->sender_id, $attribute);
        }
        return $this->successResponse([],"Đã cập nhật trạng thái comment",200);
    }
    public function update_comment(Request $request) {
        $comment = Comment::find($request->comment_id);
        $comment->content = $request->content_comment;
        $comment->save();
        return $this->successResponse([],"Đã cập nhật trạng thái comment!",200);
    }
    public function delete_comment(Request $request) {
        $comment = Comment::find($request->comment_id);
        $comment->delete();
        return $this->successResponse([],"Đã xóa comment thành công!",200);
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
            $this->addHistory($ticket->id, "Tạo yêu cầu");
            $attribute = [
                "message"=>auth()->user()->name." vừa tạo mới một yêu cầu [b]$ticket->title[/b]",
                "group_name"=>$ticket->group->group_name,
                "deadline"=>Carbon::parse($ticket->deadline)->format("d.m.Y H:i")
            ];
            if(!is_null($ticket->cc) && !empty($ticket->cc) && auth()->user()->id != $ticket->cc) {
                (new NotifyBitrix24())->sendOneMember($ticket,auth()->user()->storeToken,$request->input("cc_shortcut"),$attribute);
            }
        // notify tất cả các thành viên trong nhóm có yêu cầu mới
        (new NotifyBitrix24())->sendAllMembers($ticket,auth()->user()->storeToken, $attribute);
        if(!Helper::checkTime(Carbon::parse($request->input('ticket-deadline_shortcut')))) {
            $message = "Lịch deadline không nằm trong lịch làm việc nhân viên. Vui lòng chỉnh sửa lại deadline cho phù hợp!";
            return $this->warningResponse($ticket, $message,201);
        }
        return $this->successResponse($ticket,"Tạo yêu cầu thành công",201);
    }
}
