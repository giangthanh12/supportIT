<?php

namespace App\Http\Controllers\Api;

use App\bitrix\CRest;
use App\Http\Controllers\Controller;
use App\Helper\Helper;
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
class TicketController extends Controller
{
    use HistoryTrait;
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
    public function save(Request $request) {
        $validator = Validator::make($request->all(), [
            'name_creator'=>'required',
            'email_creator'=>'required',
            'group_id'=>'required',
            'ticket-level'=>'required',
            'ticket-title'=>'required',
            'content'=>'required',
            "ticket-deadline"=>"required"
        ]);
        if($validator->fails()) {
            return response()->json([
                'errors'=>"Đã xảy ra lỗi validate trong quá trình tạo yêu cầu"
            ],Response::HTTP_BAD_REQUEST);
        }
        $file_path = null;
        if(!is_null($request->file('ticket_file'))) {
            $file = pathinfo($request->file('ticket_file')->getClientOriginalName(), PATHINFO_FILENAME).rand(0,10).'.'.$request->file('ticket_file')->getClientOriginalExtension();
            Storage::disk("public")->putFileAs("assets/file-ticket", $request->file('ticket_file'), $file);
            $file_path = "storage/assets/file-ticket/".$file;
        }
        $ticket = Ticket::create([
            "title"=>$request->input("ticket-title"),
            "creator_id"=>Auth::id(),
            "name_creator"=>$request->name_creator,
            "deadline"=>Carbon::parse($request->input("ticket-deadline"))->format("Y-m-d H:i"),
            "cc"=>!is_null($request->input("cc")) ? $request->input("cc") : NULL,
            "email_creator"=>$request->email_creator,
            "group_id"=>$request->group_id,
            "file"=>$file_path,
            "assignees_id"=>is_null($request->input("task-assigned")) ? NULL : json_encode($request->input("task-assigned")),
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
            return response()->json([
                "status"=>"warning",
                'msg'=>"Lịch deadline không nằm trong lịch làm việc nhân viên. Vui lòng chỉnh sửa lại deadline cho phù hợp!"
            ],Response::HTTP_CREATED);
        }
        return response()->json([
            "status"=>"success",
            'msg'=>"Đã tạo yêu cầu thành công"
        ],Response::HTTP_CREATED);
    }
    // get ticket by creator
    public function getdata(Request $request) {
        $tickets = Ticket::with("user:id,name,avatar")->select("id", "title", "level","status","creator_id","created_at")

        ->when($request->has("level") && $request->level != "", function ($query) use($request) {
            return $query->where("level", $request->level);
        })
        ->when($request->has("status") && $request->status != "", function ($query) use($request) {
            if($request->status == 5)
            return $query->whereIn("status", [3,4]);
            return $query->where("status", $request->status);
        })
        ->when($request->has("time") && $request->time != "", function ($query) use($request) {
            if($request->time == "today") {
                return $query->whereDate('created_at', Carbon::today());
            }
            else if($request->time == "week") {
                return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } else if($request->time == "month")
            {
                return $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
            }
        })
        ->when($request->has("group_id") && $request->group_id != "", function ($query) use($request) {
            return $query->where("group_id", $request->group_id);
        })
        ->where(function ($query) {
            $query->where("creator_id", Auth::id())
                  ->orWhere("cc",Auth::id());
        })
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
        ->when($request->has("level") && $request->level != "", function ($query) use($request) {
            return $query->where("level", $request->level);
        })
        ->when($request->has("status") && $request->status != "", function ($query) use($request) {
                 if($request->status == 5)
            return $query->whereIn("status", [3,4]);
            return $query->where("status", $request->status);
        })
        ->when($request->has("time") && $request->time != "", function ($query) use($request) {
            if($request->time == "today") {
                return $query->whereDate('created_at', Carbon::today());
            }
            else if($request->time == "week") {
                return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } else if($request->time == "month")
            {
                return $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
            }
        })
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
        return response()->json([
            'data'=>$histories
        ],Response::HTTP_OK);
    }
    public function getComments($id_ticket) {
        $comments = Comment::select("id","content", "count_like", "sender_id", "ticket_id", "updated_at as time_ago")->where("ticket_id", $id_ticket)->with("user:id,name,avatar")->get();
        return response()->json([
            'data'=>$comments
        ],Response::HTTP_OK);
    }

    public function update(Request $request, $id) {
        $ticket = Ticket::find($id);
        if($ticket->creator_id != Auth::id()) {
            return response()->json([
                'errors'=>"Bạn không có quyền sửa yêu cầu"
            ],Response::HTTP_UNAUTHORIZED);
        }
        $validator = Validator::make($request->all(), [
            'name_creator'=>'required',
            'email_creator'=>'required',
            'group_id'=>'required',
            'ticket-level'=>'required',
            'ticket-title'=>'required',
            'ticket-deadline'=>'required',
            'content'=>'required',
        ]);
        if($validator->fails()) {
            return response()->json([
                'errors'=>"Đã xảy ra lỗi validate trong quá trình tạo yêu cầu"
            ],Response::HTTP_BAD_REQUEST);
        }
        $file_path = !empty($request->ticket_file_old) ? $request->ticket_file_old : null;
        if(!is_null($request->file('ticket_file'))) {
            $file = pathinfo($request->file('ticket_file')->getClientOriginalName(), PATHINFO_FILENAME).rand(0,10).'.'.$request->file('ticket_file')->getClientOriginalExtension();
            Storage::disk("public")->putFileAs("assets/file-ticket", $request->file('ticket_file'), $file);
            $file_path = "storage/assets/file-ticket/".$file;
        }
        if(strtotime($request->input("ticket-deadline")) > strtotime($ticket->deadline)) {
            $ticket->confirm_deadline = NULL;
        }
            $ticket->title=$request->input("ticket-title");
            $ticket->creator_id=Auth::id();
            $ticket->deadline = Carbon::parse($request->input("ticket-deadline"))->format("Y-m-d H:i");
            $ticket->cc = !is_null($request->input("cc")) ? $request->input("cc") : NULL;
            $ticket->name_creator=$request->name_creator;
            $ticket->email_creator=$request->email_creator;
            $ticket->group_id=$request->group_id;
            $ticket->file=$file_path;
            $ticket->assignees_id= !empty($request->input("task-assigned")) ? json_encode($request->input("task-assigned")) : NULL;
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
        return response()->json([
            'msg'=>"Đã cập nhật yêu cầu thành công",
            "data"=>$ticket
        ],Response::HTTP_CREATED);
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
        $NotifyBitrix24->sendOneMember($ticket,auth()->user()->storeToken,$ticket->creator_id,$attribute);
        $NotifyBitrix24->sendLeaderAndRelatedMember($ticket, auth()->user()->storeToken, $attribute);
        return response()->json([
           'msg'=> $request->status_ticket == 2 ?"Đã hủy đóng yêu cầu" : "Đã xác nhận đóng yêu cầu",
       ],Response::HTTP_OK);
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
            return response()->json([
                'msg'=>"Cập nhật thành công",
            ],Response::HTTP_OK);
        }
    // public function update_assignee(Request $request) {// Tiếp nhận yêu cầu
    //     $ticket = Ticket::find($request->id_ticket);
    //     $leader_id = Group::where("members_id", "like",'%'.'"'.Auth::id().'"'.'%')->first()->leader_id;
    //     $notifyBitrix24 = new NotifyBitrix24();
    //     $assignees_id = $ticket->assignees_id;

    //     if(is_null($assignees_id) || empty($assignees_id)) {
    //         $ticket->assignees_id = json_encode([(string) Auth::id()]);
    //         $ticket->status = $ticket->status == 1 ? 2 : $ticket->status;
    //         $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, auth()->user()->name. " đã tiếp nhận yêu cầu");
    //         if(Auth::id() != $leader_id)
    //         $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id, auth()->user()->name. " trong nhóm của bạn đã tiếp nhận yêu cầu"); // thông báo cho leader
    //     } else {
    //         $assignees_id = json_decode($assignees_id);
    //         if(in_array((string) Auth::id(), $assignees_id)) {
    //             $key = array_search((string) Auth::id(), $assignees_id);
    //             if($ticket->status == 1) {
    //                 $ticket->status = 2;
    //                 $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, auth()->user()->name. " đã tiếp nhận yêu cầu");
    //                 if(Auth::id() != $leader_id)
    //                 $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id, auth()->user()->name. " trong nhóm của bạn đã tiếp nhận yêu cầu"); // thông báo cho leader
    //             }
    //             else {
    //                 array_splice($assignees_id, $key, 1);
    //                 $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, auth()->user()->name. " đã hủy tiếp nhận yêu cầu");
    //                 if(Auth::id() != $leader_id)
    //                 $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id, auth()->user()->name. " trong nhóm của bạn đã hủy tiếp nhận yêu cầu"); // thông báo cho leader
    //             }
    //         }
    //         else {
    //             $assignees_id[] = (string) Auth::id();
    //             $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $ticket->creator_id, auth()->user()->name. " đã tiếp nhận yêu cầu"); // thông báo cho người tạo yêu cầu
    //             if(Auth::id() != $leader_id)
    //             $notifyBitrix24->sendOneMember($ticket, auth()->user()->storeToken, $leader_id, auth()->user()->name. " trong nhóm của bạn đã tiếp nhận yêu cầu"); // thông báo cho leader
    //             $ticket->status = 2;
    //         }
    //         $ticket->assignees_id = json_encode($assignees_id);
    //     }
    //     $ticket->save();
    //     return response()->json([
    //         'msg'=>"Cập nhật thành công",
    //     ],Response::HTTP_OK);
    // }
    public function check_permisiion_assign(Request $request) {
        $result = false;
        if(Helper::checkLeader(Auth::id()))
        $result = true;
        return response()->json([
            'data'=>$result,
        ],Response::HTTP_OK);
    }
    public function update_assignee_ticket(Request $request) {
        if(!Helper::checkLeader(Auth::id())) return false;
        $validator = Validator::make($request->all(), [
            'ticket-title'=>'required',
            'task-assigned'=>'required|array',
        ]);
        if($validator->fails()) {
            return response()->json([
                'errors'=>"Đã xảy ra lỗi validate trong quá trình giao việc"
            ],Response::HTTP_BAD_REQUEST);
        }
        $ticket_id = $request->get("ticket-title");
        $ticket = Ticket::find($ticket_id);
        $ticket->assignees_id = json_encode($request->get("task-assigned"));
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
        return response()->json([
            'msg'=>"Phân công việc thành công!",
        ],Response::HTTP_OK);
    }
    public function delete($id_ticket) {
        $ticket = Ticket::find($id_ticket);
        if($ticket->creator_id != Auth::id()) {
            return response()->json([
                'errors'=>"Bạn không có quyền xóa yêu cầu"
            ],Response::HTTP_UNAUTHORIZED);
        }
        $ticket->delete();
        return response()->json([
            'msg'=>"Đã xóa yêu cầu thành công",
        ],Response::HTTP_OK);
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

        return response()->json([
            'msg'=>"Bình luận thành công",
            "data"=>$dataResponse
        ],Response::HTTP_CREATED);
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
        return response()->json([
            'msg'=>$ticket->status == 3 ? "Đã chuyển trạng thái đã xử lý cho yêu cầu." : "Đã chuyển trạng đang xử lý cho yêu cầu.",
        ],Response::HTTP_OK);
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
        return response()->json([
            'msg'=>"Đã cập nhật trạng thái comment",
            "data"=>$comment
        ],Response::HTTP_OK);
    }
    public function update_comment(Request $request) {
        $comment = Comment::find($request->comment_id);
        $comment->content = $request->content_comment;
        $comment->save();
        return response()->json([
            'msg'=>"Đã cập nhật trạng thái comment",
            "data"=>$comment
        ],Response::HTTP_OK);
    }
    public function delete_comment(Request $request) {
        $comment = Comment::find($request->comment_id);
        $comment->delete();
        return response()->json([
            'msg'=>"Đã xóa comment thành công",
        ],Response::HTTP_OK);
    }
}
