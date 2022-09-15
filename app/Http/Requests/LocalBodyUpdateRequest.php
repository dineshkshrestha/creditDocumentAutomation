<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocalBodyUpdateRequest extends FormRequest
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
            'name'=>'required|max:50',

        ];
    }

    public function messages()
    {
        return[
            'name.required'=>'PLEASE ENTER LOCAL BODY NAME'  ,
            'name.max'=>'LOCAL BODY NAME MUST BE LESS THAN 50 CHARACTERS.',
            'name.unique'=>'LOCAL BODY NAME ALREADY EXIST! PLEASE ENTER ANOTHER NAME'
        ];
    }
}
