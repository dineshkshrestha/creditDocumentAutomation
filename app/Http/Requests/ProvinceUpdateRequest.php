<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProvinceUpdateRequest extends FormRequest
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
            'name'=>'required|max:50|unique:tbl_province,name,'.$this->id,
        ];
    }

    public function messages()
    {
        return[
            'name.required'=>'PLEASE ENTER PROVINCE NAME'  ,
            'name.max'=>'PROVINCE NAME MUST BE LESS THAN 50 CHARACTERS.',
            'name.unique'=>'PROVINCE NAME ALREADY EXIST! PLEASE ENTER ANOTHER NAME'
        ];
    }
}
