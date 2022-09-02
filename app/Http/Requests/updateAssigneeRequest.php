<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateAssigneeRequest extends FormRequest
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
            'ticket-title'=>'required',
            'task-assigned'=>'required|array',
        ];
    }
    public function messages()
    {
        return [
            'ticket-title.required' => 'Tiêu đề yêu cầu là bắt buộc!',
            'task-assigned.required' => 'Người được giao là bắt buộc!',
        ];
    }
}
