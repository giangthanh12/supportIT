<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShortcutRequest extends FormRequest
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
            'name_creator_shortcut'=>'required',
            'email_creator_shortcut'=>'required',
            'group_id_shortcut'=>'required',
            'ticket-level_shortcut'=>'required',
            'ticket-title_shortcut'=>'required',
            'content_shortcut'=>'required',
            "ticket-deadline_shortcut"=>"required"
        ];
    }
    public function messages()
    {
        return [
            'name_creator_shortcut.required' => 'Trường người tạo yêu cầu bắt buộc!',
            'email_creator_shortcut.required' => 'Trường email người tạo yêu cầu bắt buộc!',
            'group_id_shortcut.required' => 'Trường nhóm hỗ yêu cầu bắt buộc!',
            'ticket-level_shortcut.required' => 'Trường cấp độ yêu cầu bắt buộc!',
            'ticket-title_shortcut.required' => 'Trường tiêu đề cầu bắt buộc!',
            'content_shortcut.required' => 'Trường nội dung yêu cầu bắt buộc!',
            'ticket-deadline_shortcut.required' => 'Trường deadline yêu cầu bắt buộc!',
        ];
    }
}
