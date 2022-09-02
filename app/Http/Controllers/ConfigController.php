<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalendarSaveRequest;
use App\Http\Requests\CalendarUpdateRequest;
use App\Http\Requests\HolidayRequest;
use App\Models\Calendar;
use App\Models\Config;
use App\Models\Holiday;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class ConfigController extends Controller
{
    use ResponseTrait;
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
        return $this->successResponse([],"Cập nhập thời gian tự động đóng yêu cầu thành công!",200);
    }
    public function saveCalendar(CalendarSaveRequest $request) {
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
    public function updateCalendar(CalendarUpdateRequest $request, $id) {
        $calendarEdit = Calendar::findOrFail($id);
        $day = $request->input("day-calendar");
        $from = $request->input("from-calendar");
        $to = $request->input("to-calendar");
        $calendarEdit->update([
            "DAY"=>$day,
            "from"=>$from,
            "to"=>$to,
        ]);
        $messageSuccess = [
            "msg"=> "Cập nhập lịch làm việc thành công!",
            "status"=>"success"
        ];
        return redirect()->route("ticket.settings")->with($messageSuccess);
    }
    public function deleteCalendar($id) {
        $calendar = Calendar::findOrFail($id);
        $calendar->delete();
        $messageSuccess = [
            "msg"=> "Xóa lịch làm việc thành công!",
            "status"=>"success"
        ];
        return back()->with($messageSuccess);
    }
    public function saveHoliday(HolidayRequest $request) {
        $date = $request->input("day-holiday");
        $date = Carbon::parse($date)->format('Y-m-d');
        $title = $request->input("title-holiday");
        Holiday::create([
            "date"=>$date,
            "title"=>$title,
        ]);
        $messageSuccess = [
            "msg"=> "Thêm ngày nghỉ thành công!",
            "status"=>"success"
        ];
        return back()->with($messageSuccess);
    }
    public function editHoliday($id) {
        $holidayEdit = Holiday::findOrFail($id);
        return view("settings", compact("holidayEdit"));
    }
    public function updateHoliday(HolidayRequest $request, $id) {
        $holiday = Holiday::findOrFail($id);
        $date = $request->input("day-holiday");
        $date = Carbon::parse($date)->format('Y-m-d');
        $title = $request->input("title-holiday");
        $holiday->update([
            "date"=>$date,
            "title"=>$title,
        ]);
        $messageSuccess = [
            "msg"=> "NGày nghỉ cập nhập thành công!",
            "status"=>"success"
        ];
        return redirect()->route("ticket.settings")->with($messageSuccess);
    }
    public function deleteHoliday($id) {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();
        $messageSuccess = [
            "msg"=> "Xóa ngày nghỉ thành công!",
            "status"=>"success"
        ];
        return back()->with($messageSuccess);
    }
}
