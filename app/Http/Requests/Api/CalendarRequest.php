<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CalendarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(!is_null($this->calendarId)) {
            return [
                'day_calendar' => 'required|unique:calendars,DAY,'.$this->calendarId,
                'from_calendar' => 'required',
                'to_calendar' => 'required',
            ];
        }
        return [
            'day_calendar' => 'required|unique:calendars,DAY',
            'from_calendar' => 'required',
            'to_calendar' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'day_calendar.required'  => '(*) Thứ yêu cầu bắt buộc!',
            'day_calendar.unique'  => '(*) Thứ yêu cầu duy nhất!',
            'from_calendar.required'  => '(*) Thời gian bắt đầu yêu cầu bắt buộc!',
            'to_calendar.required'  => '(*) Thời gian kết thúc yêu cầu bắt buộc!',
        ];
    }
}
