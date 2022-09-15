<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LandUpdateRequest extends FormRequest
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
            'district_id'=>'required',
            'local_body_id'=>'required',
            'wardno'=>'required',
            'sheet_no'=>'max:20',
            'kitta_no'=>'required|max:20',
            'area'=>'required|max:20',
            'remarks'=>'max:20',
            'malpot'=>'required|max:20',
        ];
    }

    public function messages()
    {
        return [
            'district_id.required'=>'PLEASE SELECT DISTRICT',
            'local_body_id.required'=>'PLEASE SELECT LOCAL BODY',
            'wardno.required' => 'PLEASE SELECT WARD NO',
            'sheet_no.required' => 'ENTER SHEET NUMBER',
            'sheet_no.max' => 'ENTER SHEET NUMBER LESS THAN 20 CHARACTERS',
            'kitta_no.required' => 'ENTER KITTA NUMBER',
            'kitta_no.max' => 'ENTER KITTA NUMBER LESS THAN 20 CHARACTERS',
            'area.required' => 'ENTER AREA',
            'area.max' => 'ENTER AREA LESS THAN 20 CHARACTERS',
            'remarks.required' => 'ENTER REMARKS',
            'remarks.max' => 'ENTER REMARKS LESS THAN 20 CHARACTERS',
            'malpot.required' => 'ENTER MALPOT',
            'malpot.max' => 'ENTER MALPOT LESS THAN 20 CHARACTERS',
        ];
    }
}
