<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Calendar;
use App\Models\Config;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

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
        return view("api.settings");
    }
    public function getDataHolidays() {
        $holidays = Holiday::select("id", "date as date_format", "title")->orderBy('date', 'asc')->get();
        return response()->json([
            'data'=>$holidays
        ],Response::HTTP_OK);
    }

    public function getDataCalendars() {
        $data = [];
        $calendars = Calendar::get()
        ->sortBy(function ($calendar) {
            return constant('\Carbon\Carbon::' . strtoupper($calendar->DAY));
        });
        foreach ($calendars as $calendar) {
            $data[] = $calendar;
        }
        return response()->json([
            'data'=>$data
        ],Response::HTTP_OK);
    }
    public function saveTimeClose(Request $request) {
        Config::updateOrCreate(
            ['cfg_key' => "timeclose"],
            ['cfg_value' => $request->timeclose]
        );
        return "Time close update successfull";
    }
    public function saveCalendar(Request $request) {
        if(!empty($request->calendarId)) {
            $validator = Validator::make($request->all(), [
                'day_calendar' => 'required|unique:calendars,DAY,'.$request->calendarId,
                'from_calendar' => 'required',
                'to_calendar' => 'required',
            ]);
        }
        else {
            $validator = Validator::make($request->all(), [
                'day_calendar' => 'required|unique:calendars,DAY',
                'from_calendar' => 'required',
                'to_calendar' => 'required',
            ]);
        }
        if ($validator->fails()) {
            return response()->json([
                'error'=>"Lỗi validate!"
            ],Response::HTTP_BAD_REQUEST);
        }
        $day = $request->input("day_calendar");
        $from = $request->input("from_calendar");
        $to = $request->input("to_calendar");
        $calendar = new Calendar();
        if(!empty($request->calendarId)) {
            $calendar = Calendar::find($request->calendarId);
        }
        $calendar->DAY = $day;
        $calendar->from = $from;
        $calendar->to = $to;
        $calendar->save();
        return response()->json([
            'msg'=>empty($request->calendarId) ? "Thêm lịch làm việc thành công!" : "Cập nhật lịch làm việc thành công",
        ],Response::HTTP_CREATED);
    }
    public function detailCalendar($id) {
        $calendarEdit = Calendar::select("id","DAY", "from", "to")->where("id", $id)->first();
        return response()->json([
            'data'=>$calendarEdit,
        ],Response::HTTP_OK);
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
        return response()->json([
            'msg'=>"Xóa dữ liệu thành công",
        ],Response::HTTP_OK);
    }
    public function saveHoliday(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'date' => 'required',
        ],
        [
            'title.required'  => '(*) Day holiday is required',
            'date.required'  => '(*) Title holiday is required',
        ]);
        if ($validator->fails()) {
            $messageError = [
                "msg"=> "Lỗi validate!",
                "status"=>"fail"
            ];
            return response()->json([
                'error'=>$messageError
            ],Response::HTTP_BAD_REQUEST);
        }
        $date = $request->input("date");
        $date = Carbon::parse($date)->format('Y-m-d');
        $title = $request->input("title");
        $holiday = new Holiday();
        if(!empty($request->holiday_id)) {
            $holiday = Holiday::find($request->holiday_id);
        }
        $holiday->date = $date;
        $holiday->title = $title;
        $holiday->save();
        return response()->json([
            'msg'=>empty($request->holiday_id) ? "Thêm ngày nghỉ thành công!" : "Cập nhật ngày nghỉ thành công",
        ],Response::HTTP_CREATED);
    }
    public function detailHoliday($id) {
        $holidayEdit = Holiday::select("id","title", "date as date_format")->where("id", $id)->first();
        return response()->json([
            'data'=>$holidayEdit,
        ],Response::HTTP_OK);
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
        return response()->json([
            'msg'=>"Xóa dữ liệu thành công",
        ],Response::HTTP_OK);
    }
}
