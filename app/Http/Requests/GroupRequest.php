<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupRequest extends FormRequest
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
            'group_name'=>'required',
            "memberIds"=>"array",
            "leader_id" => Rule::in($this->memberIds)
        ];
    }
    public function messages()
    {
        return [
            'group_name.required' => 'Tên nhóm yêu cầu bắt buộc!',
            'leader_id.in' => 'Trưởng nhóm phải nằm trong danh sách các thành viên',
        ];
    }
}
