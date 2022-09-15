<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleUpdateRequest extends FormRequest
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
            'model_number' => 'required|max:20',
            'registration_number' => 'required|max:20',
            'engine_number' => 'required|max:20',
            'chassis_number' => 'required|max:20'
        ];
    }

    public function messages()
    {
        return [
            'model_number.max' => 'PLEASE ENTER MODEL NUMBER',
            'model_number.required' => 'MODEL NUMBER SHOULD BE LESS THAN 20 CHARACTERS.',
            'registration_number.max' => 'PLEASE ENTER REGISTRATION NUMBER',
            'registration_number.required' => 'REGISTRATION NUMBER SHOULD BE LESS THAN 20 CHARACTERS.',
            'engine_number.max' => 'PLEASE ENTER ENGINE NUMBER',
            'engine_number.required' => 'ENGINE NUMBER SHOULD BE LESS THAN 20 CHARACTERS.',
            'chassis_number.max' => 'PLEASE ENTER CHASSIS NUMBER',
            'chassis_number.required' => 'CHASSIS NUMBER SHOULD BE LESS THAN 20 CHARACTERS.'
        ];
    }
}
