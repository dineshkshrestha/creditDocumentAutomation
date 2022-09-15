<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalLoanUpdateRequest extends FormRequest
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
            'amount'=>'required',
            'words'=>'required|max:100',
            'branch_id'=>'required',

        ];
    }

    public function messages()
    {
        return [
            'branch_id.required' => 'PLEASE CHOOSE BRANCH',
            'words.required' => 'ENTER LOAN AMOUNT IN WORDS',
            'words.max' => 'ENTER LOAN AMOUNT LESS THAN 100 CHARACTERS',
            'amount.required' => 'ENTER LOAN AMOUNT',
            'amount.numeric' => 'LOAN AMOUNT MUST BE NUMBER',
              ];
    }
}
