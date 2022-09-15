<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorporateGuarantorCreateRequest extends FormRequest
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
    {  $d = $this->get('a_dob_year');
        $dobyear = $d + 16;

        return [
            'english_name' => 'required|max:100',
            'nepali_name' => 'required|max:100',
            'client_id' => 'max:30',
            'district_id' => 'required',
            'local_bodies_id' => 'required',
            'registration_number' => 'required|max:20|unique:tbl_corporate_guarantor',
            'phone' => 'nullable|min:7',


            'a_english_name' => 'required|max:30',
            'a_nepali_name' => 'required|max:50',
            'a_grandfather_name' => 'required|max:50',
            'a_grandfather_relation' => 'required',
            'a_father_name' => 'required|max:50',
            'a_father_relation' => 'required',
            'a_spouse_name' => 'max:50',
            'a_district_id' => 'required',
            'a_post' => 'required|max:30',
            'a_local_bodies_id' => 'required',
            'a_citizenship_number' => 'required|max:20',
            'a_issued_district' => 'required',
            'a_issued_year' => 'numeric|min:' . $dobyear,
        ];
    }

    public function messages()
    {
        return [
            'client_id.max' => 'ENTER LESS THAN 30 CHARACTERS',
            'english_name.required' => 'ENTER ENGLISH NAME',
            'english_name.max' => 'ENTER ENGLISH NAME LESS THAN 100 CHARACTERS',
            'nepali_name.required' => 'ENTER NEPALI NAME',
            'nepali_name.max' => 'ENTER NEPALI NAME LESS THAN 100 CHARACTERS',
            'registration_number.required' => 'PLEASE ENTER REGISTRATION NUMBER',
            'registration_number.max' => 'ENTER REGISTRATION NUMBER LESS THAN 20 CHARACTERS',
            'registration_number.unique' => 'ALREADY EXIST! ENTER UNIQUE REGISTRATION NUMBER',
            'local_bodies_id.required' => 'PLEASE CHOOSE LOCAL BODY',
            'district_id.required' => 'PLEASE ENTER DISTRICT NAME',
            'a_issued_year.min' => 'ISSUED YEAR MUST BE MORE THAN 16 YEARS FROM DATE OF BIRTH',
            'phone.min' => 'PHONE NUMBER SHOULD BE MORE THAN 7 CHARACTERS',

            'a_english_name.required' => 'ENTER AUTHORIZED PERSON ENGLISH NAME',
            'a_english_name.max' => 'ENTER AUTHORIZED PERSON ENGLISH NAME LESS THAN 50 CHARACTERS',
            'a_nepali_name.required' => 'ENTER AUTHORIZED PERSON NEPALI NAME',
            'a_nepali_name.max' => 'ENTER AUTHORIZED PERSON NEPALI NAME LESS THAN 50 CHARACTERS',
            'a_grandfather_name.required' => 'ENTER GRANDFATHER NAME',
            'a_grandfather_name.max' => 'ENTER AUTHORIZED PERSON GRANDFATHER NAME LESS THAN 50 CHARACTERS',
            'a_father_name.required' => 'ENTER AUTHORIZED PERSON FATHER NAME',
            'a_father_name.max' => 'ENTER AUTHORIZED PERSON FATHERS NAME LESS THAN 50 CHARACTERS',
            'a_spouse_name.required' => 'ENTER AUTHORIZED PERSON SPOUSE NAME',
            'a_spouse_name.max' => 'ENTER AUTHORIZED PERSON SPOUSE NAME LESS THAN 50 CHARACTERS',
            'a_issued_district.required' => 'PLEASE CHOOSE AUTHORIZED PERSON DISTRICT',
            'a_citizenship_number.required' => 'PLEASE ENTER AUTHORIZED PERSON CITIZENSHIP NUMBER',
            'a_citizenship_number.max' => 'ENTER AUTHORIZED PERSON CITIZENSHIP NUMBER LESS THAN 20 CHARACTERS',
            'a_local_bodies_id.required' => 'PLEASE CHOOSE AUTHORIZED PERSON LOCAL BODY',
            'a_district_id.required' => 'PLEASE ENTER AUTHORIZED PERSON DISTRICT NAME',
            'a_post.required' => 'ENTER POST',
            'a_post.max' => 'ENTER LESS THAN 30 CHARACTERS',
        ];
    }
}
