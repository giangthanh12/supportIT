<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request) {
        $group_tickets = Group::select("id","group_name","leader_id")->with("user:id,name,email")->withCount([
            'ticket as totalNotDone' => function ($query) use($request) {
                $query->where('status', 1);
            }
        ])->get();
        $auth_id = '"'.Auth::id().'"';
        $groups_id = Group::where("members_id", "like",'%'.$auth_id.'%')->pluck("id");

        $total_assign_tickets = 0;
        $total_new_tickets = 0;
        $total_tickets_done = 0;
        $total_tickets_notdone = 0;
        if(count($groups_id) > 0) {
            $total_assign_tickets = Ticket::where("status", 2)->whereIn('group_id', $groups_id)->get()->count();
            $total_new_tickets = Ticket::where("status", 1)->whereIn('group_id', $groups_id)->get()->count();
            $total_tickets_done = Ticket::whereIn("status", [3,4])->whereIn('group_id', $groups_id)->get()->count();
            $total_tickets_notdone = Ticket::where("status", 2)->whereIn('group_id', $groups_id)->get()->count();
        }
        // via user
        $total_new_tickets_user = Ticket::where(function ($query) {
            $query->where("creator_id", Auth::id())
                  ->orWhere("cc",Auth::id());
        })
        ->where("status", 1)->get()->count();
        $total_tickets_done_user = Ticket::where(function ($query) {
            $query->where("creator_id", Auth::id())
                  ->orWhere("cc",Auth::id());
        })->whereIn("status", [3,4])->get()->count();
        $total_tickets_notdone_user = Ticket::where(function ($query) {
            $query->where("creator_id", Auth::id())
                  ->orWhere("cc",Auth::id());
        })->where("status", 2)->get()->count();

        $groups = Group::get(["id", "group_name"]);
        // total
        return view("dashboard", compact("groups", "group_tickets","total_new_tickets_user","total_tickets_done_user","total_tickets_notdone_user", "total_assign_tickets", "total_new_tickets", "total_tickets_done", "total_tickets_notdone"));
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
    function get_ticketNotDone(Request $request) {
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
