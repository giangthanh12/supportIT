<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends FormRequest
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
        return [
            'day-holiday' => 'required',
            'title-holiday' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'day-holiday.required'  => '(*) Ngày nghỉ yêu cầu bắt buộc!',
            'title-holiday.required'  => '(*) Tiêu đề ngày nghỉ yêu cầu bắt buộc!',
        ];
    }
}
