<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MinistryCreateRequest extends FormRequest
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
            'name'=>'required|max:50|unique:tbl_ministry',

        ];
    }

    public function messages()
    {
        return[
            'name.required'=>'PLEASE ENTER MINISTRY NAME'  ,
            'name.max'=>'MINISTRY NAME MUST BE LESS THAN 50 CHARACTERS.',
            'name.unique'=>'MINISTRY NAME ALREADY EXIST! PLEASE ENTER ANOTHER NAME'
        ];
    }
}
