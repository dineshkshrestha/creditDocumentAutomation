<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalFacilityUpdateRequest extends FormRequest
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
            'amount' => 'required',
            'rate' => 'required',
            'remarks' => 'max:100',
            'tenure' => 'max:100',


        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'ENTER LOAN AMOUNT',
            'rate.required' => 'ENTER RATE',
            'remarks.max' => 'REMARKS MUST BE LESS THAN 50 CHARACTERS.',
            'tenure.max' => 'TENURE MUST BE LESS THAN 50 CHARACTERS.',
        ];
    }
}
