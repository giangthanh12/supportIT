<?php

namespace App\Http\Requests;

use App\Models\Calendar;
use Illuminate\Foundation\Http\FormRequest;

class CalendarUpdateRequest extends FormRequest
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
        $id = $this->route()->parameters["id"];
        return [
            'day-calendar' => 'required|unique:calendars,DAY,'.$id,
            'from-calendar' => 'required',
            'to-calendar' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'day-calendar.required'  => '(*) Thứ yêu cầu bắt buộc!',
            'day-calendar.unique'  => '(*) Thứ yêu cầu duy nhất!',
            'from-calendar.required'  => '(*) Thời gian bắt đầu yêu cầu bắt buộc!',
            'to-calendar.required'  => '(*) Thời gian kết thúc yêu cầu bắt buộc!',
        ];
    }
}
