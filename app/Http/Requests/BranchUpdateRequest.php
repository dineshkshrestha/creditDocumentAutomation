<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class BranchUpdateRequest extends FormRequest
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
            'location' => 'required|max:50|unique:tbl_branch,location,'.$this->id,
            'district' => 'required|max:50 ',
            'local_body' => 'required|max:50',
        ];
    }

    public function messages()
    {
        return [
            'location.required' => 'PLEASE ENTER LOCATION',
            'location.max' => 'LOCATION MUST BE LESS THAN 50 CHARACTERS.',
            'location.unique' => 'LOCATION ALREADY EXIST! PLEASE ENTER ANOTHER NAME',
            'district.required' => 'PLEASE ENTER DISTRICT',
            'district.max' => 'DISTRICT MUST BE LESS THAN 50 CHARACTERS.',
            'local_body.required' => 'PLEASE ENTER LOCAL BODY',
            'local_body.max' => 'LOCAL BODY MUST BE LESS THAN 50 CHARACTERS.',
        ];
    }

}
