<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class DashboardController extends Controller
{
    public function index(Request $request) {
        $token = $request->has("token") ? $request->get("token") : null;
        $groups = Group::get(["id", "group_name"]);
        return view("api.dashboard", compact("groups","token"));
    }
    public function get_ticket_month(Request $request) {
        $stastic = Ticket::select(DB::raw('count(id) as total'), DB::raw('MONTH(created_at) as month'))
        ->groupBy('month')
        ->when($request->has("group_id") && $request->group_id != "", function ($query) use($request) {
            return $query->where("group_id", $request->group_id);
        })
        ->whereYear('created_at', '=', Carbon::now()->format("Y"))
        ->get();
        $year = [0,0,0,0,0,0,0,0,0,0,0,0]; // giá trị mặc định ban đầu
        foreach($stastic as $key) {
        $year[$key->month-1] = $key->total;
        }
        return $year;
    }
    public function get_info_ticket() {
        $data = [];
        $auth_id = '"'.Auth::id().'"';
        $groups_id = Group::where("members_id", "like",'%'.$auth_id.'%')->pluck("id");
        $total_assign_tickets = Ticket::where("status", 2)->whereIn('group_id', $groups_id)->get()->count();
        // via support
        $total_new_tickets = Ticket::whereIn('group_id', $groups_id)->where("status", 1)->get()->count();
        $total_tickets_done = Ticket::whereIn('group_id', $groups_id)->whereIn("status", [3,4])->get()->count();
        $total_tickets_notdone = Ticket::whereIn('group_id', $groups_id)->where("status", 2)->get()->count();
          // via user
          $total_new_tickets_user = Ticket::query()->where("status", 1)->where("creator_id", Auth::id())->orWhere("cc",Auth::id())->get()->count();
          $total_tickets_done_user = Ticket::whereIn("status", [3,4])->where("creator_id", Auth::id())->orWhere("cc",Auth::id())->get()->count();
          $total_tickets_notdone_user = Ticket::where("status", 2)->where("creator_id", Auth::id())->orWhere("cc",Auth::id())->get()->count();
        $data["total_assign_tickets"] = $total_assign_tickets;
        $data["total_new_tickets"] = $total_new_tickets;
        $data["total_tickets_done"] = $total_tickets_done;
        $data["total_tickets_notdone"] = $total_tickets_notdone;

        $data["total_new_tickets_user"] = $total_new_tickets_user;
        $data["total_tickets_done_user"] = $total_tickets_done_user;
        $data["total_tickets_notdone_user"] = $total_tickets_notdone_user;
        return response()->json([
            'data'=>$data
        ],Response::HTTP_OK);
    }
    public function get_ticketNotDone( Request $request) {
        $group_tickets = Group::select("id","group_name","leader_id")->with("user:id,name,email")->withCount([
            'ticket as totalNotDone' => function ($query) use($request) {
                $query->when($request->has("date"), function ($query) use($request) {
                    if($request->date == "today") {
                        return $query->whereDate('created_at', Carbon::today());
                    }
                    else if($request->date == "week") {
                        return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    } else if($request->date == "month")
                    {
                        return $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    }
                })
                ->where('status', 2);
            },
            'ticket as totalDone' => function ($query) use($request) {
                $query->when($request->has("date"), function ($query) use($request) {
                    if($request->date == "today") {
                        return $query->whereDate('created_at', Carbon::today());
                    }
                    else if($request->date == "week") {
                        return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    } else if($request->date == "month")
                    {
                        return $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    }
                })
                ->whereIn('status', [3,4]);
            },
            'ticket as totalPending' => function ($query) use($request) {
                $query->when($request->has("date"), function ($query) use($request) {
                    if($request->date == "today") {
                        return $query->whereDate('created_at', Carbon::today());
                    }
                    else if($request->date == "week") {
                        return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    } else if($request->date == "month")
                    {
                        return $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    }
                })
                ->where('status', 1);
            }
        ])->get();
        return $group_tickets;
    }
    function get_statistic_staff($id_group) {
        $group = Group::find($id_group);
        $assignees_id = json_decode($group->members_id);
        $data = [];
        foreach($assignees_id as $assignee_id) {
           $name = User::find($assignee_id)->name;
           $ticketPending = Ticket::where("status", 1)->where('assignees_id', "like",'%'.$assignee_id.'%' )->get()->count();
           $ticketNotDone = Ticket::where("status", 2)->where('assignees_id', "like",'%'.$assignee_id.'%' )->get()->count();
           $data[] = [
            "name"=>$name,
            "ticketPending"=>$ticketPending,
            "ticketNotDone"=>$ticketNotDone,
           ];
        }
        return $data;
    }
}
