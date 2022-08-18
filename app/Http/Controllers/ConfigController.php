<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Config;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class ConfigController extends Controller
{
    public function __construct()
    {
        $calendars = Calendar::all()
        ->sortBy(function ($calendar) {
            return constant('\Carbon\Carbon::' . strtoupper($calendar->DAY));
        });
        $holidays = Holiday::orderBy('date', 'asc')->get();
        $timeclose = Config::find("timeclose");
        View::share("calendars", $calendars);
        View::share("holidays", $holidays);
        View::share("timeclose", $timeclose);
    }
    public function index() {

        return view("settings");
    }
    public function saveTimeClose(Request $request) {
        Config::updateOrCreate(
            ['cfg_key' => "timeclose"],
            ['cfg_value' => $request->timeclose]
        );
        return "Time close update successfull";
    }
    public function saveCalendar(Request $request) {
        $validator = Validator::make($request->all(), [
            'day-calendar' => 'required|unique:calendars,DAY',
            'from-calendar' => 'required',
            'to-calendar' => 'required',
        ],
        [
            'day-calendar.required'  => '(*) Day calendar is required',
            'day-calendar.unique'  => '(*) Day calendar exists',
            'from-calendar.required'  => '(*) From calendar is required',
            'to-calendar.required'  => '(*) To calendar is required',
        ]);
        if ($validator->fails()) {
            $messageError = [
                "msg"=> "Calendar update unsuccessful!",
                "status"=>"fail"
            ];
             return redirect()
             ->back()->with($messageError)->withErrors($validator)->withInput();
        }
        $day = $request->input("day-calendar");
        $from = $request->input("from-calendar");
        $to = $request->input("to-calendar");
        Calendar::create([
            "DAY"=>$day,
            "from"=>$from,
            "to"=>$to,
        ]);
        return back();
    }
    public function editCalendar($id) {
        $calendarEdit = Calendar::findOrFail($id);
        return view("settings", compact("calendarEdit"));
    }
    public function updateCalendar(Request $request, $id) {
        $calendarEdit = Calendar::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'day-calendar' => 'required|unique:calendars,DAY,'.$id,
            'from-calendar' => 'required',
            'to-calendar' => 'required',
        ],
        [
            'day-calendar.required'  => '(*) Day calendar is required',
            'day-calendar.unique'  => '(*) Day calendar exists',
            'from-calendar.required'  => '(*) From calendar is required',
            'to-calendar.required'  => '(*) To calendar is required',
        ]);
        if ($validator->fails()) {
            $messageError = [
                "msg"=> "Calendar update unsuccessful!",
                "status"=>"fail"
            ];
             return redirect()
             ->back()->with($messageError)->withErrors($validator)->withInput();
        }
        $day = $request->input("day-calendar");
        $from = $request->input("from-calendar");
        $to = $request->input("to-calendar");
        $calendarEdit->update([
            "DAY"=>$day,
            "from"=>$from,
            "to"=>$to,
        ]);
        $messageSuccess = [
            "msg"=> "Calendar update successful!",
            "status"=>"success"
        ];
        return redirect()->route("ticket.settings")->with($messageSuccess);
    }
    public function deleteCalendar($id) {
        $calendar = Calendar::findOrFail($id);
        $calendar->delete();
        $messageSuccess = [
            "msg"=> "Calendar delete successful!",
            "status"=>"success"
        ];
        return back()->with($messageSuccess);
    }
    public function saveHoliday(Request $request) {
        $validator = Validator::make($request->all(), [
            'day-holiday' => 'required',
            'title-holiday' => 'required',
        ],
        [
            'day-holiday.required'  => '(*) Day holiday is required',
            'title-holiday.required'  => '(*) Title holiday is required',
        ]);
        if ($validator->fails()) {
            $messageError = [
                "msg"=> "Holiday add unsuccessful!",
                "status"=>"fail"
            ];
             return redirect()
             ->back()->with($messageError)->withErrors($validator)->withInput();
        }
        $date = $request->input("day-holiday");
        $date = Carbon::parse($date)->format('Y-m-d');
        $title = $request->input("title-holiday");
        Holiday::create([
            "date"=>$date,
            "title"=>$title,
        ]);
        $messageSuccess = [
            "msg"=> "Holiday update successful!",
            "status"=>"success"
        ];
        return back()->with($messageSuccess);
    }
    public function editHoliday($id) {
        $holidayEdit = Holiday::findOrFail($id);
        return view("settings", compact("holidayEdit"));
    }
    public function updateHoliday(Request $request, $id) {
        $holiday = Holiday::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'day-holiday' => 'required',
            'title-holiday' => 'required',
        ],
        [
            'day-holiday.required'  => '(*) Day holiday is required',
            'title-holiday.required'  => '(*) Title holiday is required',
        ]);
        if ($validator->fails()) {
            $messageError = [
                "msg"=> "Holiday add unsuccessful!",
                "status"=>"fail"
            ];
             return redirect()
             ->back()->with($messageError)->withErrors($validator)->withInput();
        }
        $date = $request->input("day-holiday");
        $date = Carbon::parse($date)->format('Y-m-d');
        $title = $request->input("title-holiday");
        $holiday->update([
            "date"=>$date,
            "title"=>$title,
        ]);
        $messageSuccess = [
            "msg"=> "Holiday update successful!",
            "status"=>"success"
        ];
        return redirect()->route("ticket.settings")->with($messageSuccess);
    }
    public function deleteHoliday($id) {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();
        $messageSuccess = [
            "msg"=> "Holiday delete successful!",
            "status"=>"success"
        ];
        return back()->with($messageSuccess);
    }
}
