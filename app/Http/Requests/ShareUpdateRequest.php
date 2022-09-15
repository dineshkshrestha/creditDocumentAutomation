<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShareUpdateRequest extends FormRequest
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
            'kitta'=>'required|max:20',
            'isin'=>'required|max:20',
            'dpid'=>'required|max:20',
            'client_id'=>'max:20',
        ];
    }

    public function messages()
    {
        return [

            'kitta.required' => 'ENTER KITTA NUMBER',
            'kitta.max' => 'ENTER KITTA NUMBER LESS THAN 20 CHARACTERS',
            'isin.required' => 'ENTER ISIN',
            'isin.max' => 'ENTER ISIN LESS THAN 20 CHARACTERS',
            'dpid.required' => 'ENTER DPID',
            'dpid.max' => 'ENTER DPID LESS THAN 20 CHARACTERS',
            'client_id.required' => 'ENTER CLIENT ID',
            'client_id.max' => 'ENTER  CLIENT ID LESS THAN 20 CHARACTERS',
        ];
    }
}
