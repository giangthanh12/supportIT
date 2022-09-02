<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CalendarRequest;
use App\Http\Requests\HolidayRequest;
use App\Models\Calendar;
use App\Models\Config;
use App\Models\Holiday;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

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
        return view("api.settings");
    }
    public function getDataHolidays() {
        $holidays = Holiday::select("id", "date as date_format", "title")->orderBy('date', 'asc')->get();
        return $this->successResponse($holidays,"Lấy dữ liệu thành công",200);
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
        return $this->successResponse($data,"Lấy dữ liệu thành công!",200);
    }
    public function saveTimeClose(Request $request) {
        Config::updateOrCreate(
            ['cfg_key' => "timeclose"],
            ['cfg_value' => $request->timeclose]
        );
        return $this->successResponse([],"Cập nhập thời gian tự động đóng yêu cầu thành công!",200);
    }
    public function saveCalendar(CalendarRequest $request) {
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
        $msg = empty($request->calendarId) ? "Thêm lịch làm việc thành công!" : "Cập nhật lịch làm việc thành công";
        return $this->successResponse([],$msg,200);
    }
    public function detailCalendar($id) {
        $calendarEdit = Calendar::select("id","DAY", "from", "to")->where("id", $id)->first();
        return $this->successResponse($calendarEdit,"Lấy dữ liệu thành công",200);
    }
    public function deleteCalendar($id) {
        $calendar = Calendar::findOrFail($id);
        $calendar->delete();
        return $this->successResponse([],"Xóa dữ liệu thành công",200);
    }
    public function saveHoliday(HolidayRequest $request) {
        $date = $request->input("day-holiday");
        $date = Carbon::parse($date)->format('Y-m-d');
        $title = $request->input("title-holiday");
        $holiday = new Holiday();
        if(!empty($request->holiday_id)) {
            $holiday = Holiday::find($request->holiday_id);
        }
        $holiday->date = $date;
        $holiday->title = $title;
        $holiday->save();
        $msg = empty($request->holiday_id) ? "Thêm ngày nghỉ thành công!" : "Cập nhật ngày nghỉ thành công";
        return $this->successResponse([],$msg,200);
    }
    public function detailHoliday($id) {
        $holidayEdit = Holiday::select("id","title", "date as date_format")->where("id", $id)->first();
        return response()->json([
            'data'=>$holidayEdit,
        ],Response::HTTP_OK);
        return $this->successResponse($holidayEdit,"Lấy dữ liệu thành công.",200);
    }
    public function deleteHoliday($id) {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();
        return $this->successResponse([],"Xóa dữ liệu thành công.",200);
    }
}
