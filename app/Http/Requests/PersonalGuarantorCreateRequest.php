<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalGuarantorCreateRequest extends FormRequest
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
        $d = $this->get('dob_year');
        $dobyear = $d + 16;

        return [
            'english_name'=>'required|max:30',
            'nepali_name'=>'required|max:50',
            'client_id'=>'max:30',
            'grandfather_name'=>'required|max:50',
            'grandfather_relation'=>'required',
            'father_name'=>'required|max:50',
            'father_relation'=>'required',
            'spouse_name'=>'max:50',
            'phone' => 'nullable|min:7',

            'district_id'=>'required',
            'local_bodies_id'=>'required',
            'citizenship_number'=>'required|max:20',
            'issued_district'=>'required',
            'issued_year' => 'numeric|min:' . $dobyear,

        ];

    }

    public function messages()
    {
        return [
            'client_id.max' => 'ENTER LESS THAN 30 CHARACTERS',
            'english_name.required' => 'ENTER ENGLISH NAME',
            'english_name.max' => 'ENTER ENGLISH NAME LESS THAN 50 CHARACTERS',
            'nepali_name.required' => 'ENTER NEPALI NAME',
            'nepali_name.max' => 'ENTER NEPALI NAME LESS THAN 50 CHARACTERS',
            'grandfather_name.required' => 'ENTER GRANDFATHER NAME',
            'grandfather_name.max' => 'ENTER GRANDFATHER NAME LESS THAN 50 CHARACTERS',
            'father_name.required' => 'ENTER FATHER NAME',
            'phone.required' => 'ENTER PHONE NUMBER',
            'phone.min' => 'PHONE NUMBER SHOULD BE MORE THAN 7 CHARACTERS',
            'father_name.max' => 'ENTER FATHERS NAME LESS THAN 50 CHARACTERS',
            'spouse_name.required' => 'ENTER SPOUSE NAME',
            'spouse_name.max' => 'ENTER SPOUSE NAME LESS THAN 50 CHARACTERS',
            'issued_district.required' => 'PLEASE CHOOSE DISTRICT',
            'citizenship_number.required' => 'PLEASE ENTER CITIZENSHIP NUMBER',
            'citizenship_number.max' => 'ENTER CITIZENSHIP NUMBER LESS THAN 20 CHARACTERS',
            'citizenship_number.unique' => 'ALREADY EXIST! ENTER UNIQUE CITIZENSHIP NUMBER',
            'local_bodies_id.required' => 'PLEASE CHOOSE LOCAL BODY',
            'district_id.required' => 'PLEASE ENTER DEPARTMENT NAME',
            'issued_year.min' => 'ISSUED YEAR MUST BE MORE THAN 16 YEARS FROM DATE OF BIRTH',


            ];
    }
}
