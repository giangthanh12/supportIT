<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
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
            'name_creator'=>'required',
            'email_creator'=>'required',
            'group_id'=>'required',
            'ticket-level'=>'required',
            'ticket-title'=>'required',
            'content'=>'required',
            "ticket-deadline"=>"required",
        ];
    }
    public function messages()
    {
        return [
            'name_creator.required' => 'Trường người tạo yêu cầu bắt buộc!',
            'email_creator.required' => 'Trường email người tạo yêu cầu bắt buộc!',
            'group_id.required' => 'Trường nhóm hỗ yêu cầu bắt buộc!',
            'ticket-level.required' => 'Trường cấp độ yêu cầu bắt buộc!',
            'ticket-title.required' => 'Trường tiêu đề cầu bắt buộc!',
            'content.required' => 'Trường nội dung yêu cầu bắt buộc!',
            'ticket-deadline.required' => 'Trường deadline yêu cầu bắt buộc!',
        ];
    }
}
