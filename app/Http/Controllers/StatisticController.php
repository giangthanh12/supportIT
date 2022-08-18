<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function getData(Request $request) {
        $dataResponse = [];
        $data = Group::select("id","group_name","leader_id")->with("user:id,name,email")->withCount([
            'ticket as total' => function ($query) use($request) {
                $query->when($request->has("date"), function ($query) use($request) {
                    $month_year = explode("-", $request->date);
                    return $query->whereMonth('created_at', $month_year[0])->whereYear('created_at', $month_year[1]);
                })
                ->when($request->has("from") && $request->has("to"), function ($query) use($request) {
                    return $query->whereBetween("created_at", [Carbon::parse($request->from)->format("Y-m-d"), Carbon::parse($request->to)->format("Y-m-d")]);
                })
                ->whereIn('status', [1,2,3,4]);
            },
            'ticket as totalDone' => function ($query) use($request) {
                $query->when($request->has("date"), function ($query) use($request) {
                    $month_year = explode("-", $request->date);
                    return $query->whereMonth('created_at', $month_year[0])->whereYear('created_at', $month_year[1]);
                })
                ->when($request->has("from") && $request->has("to"), function ($query) use($request) {
                    return $query->whereBetween("created_at", [Carbon::parse($request->from)->format("Y-m-d"), Carbon::parse($request->to)->format("Y-m-d")]);
                })
                ->whereIn('status', [3,4]);
            },
            'ticket as totalNotDone' => function ($query) use($request) {
                $query->when($request->has("date"), function ($query) use($request) {
                    $month_year = explode("-", $request->date);
                    return $query->whereMonth('created_at', $month_year[0])->whereYear('created_at', $month_year[1]);
                })
                ->when($request->has("from") && $request->has("to"), function ($query) use($request) {
                    return $query->whereBetween("created_at", [Carbon::parse($request->from)->format("Y-m-d"), Carbon::parse($request->to)->format("Y-m-d")]);
                })
                ->whereIn('status', [1,2]);
            }
        ])->get();
        $dataResponse['data'] = $data;
        return $dataResponse;
    }
    public function index() {
        $total = Ticket::get()->count();
        $totalDone = Ticket::whereIn("status", [3,4])->get()->count();
        $totalNotDone = Ticket::whereIn("status", [1,2])->get()->count();
        return view("statistic", compact("total","totalDone","totalNotDone"));
    }
}

