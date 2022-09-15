<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PdfMergeCreateRequest extends FormRequest
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
        $files = count($this->input('files'));
        foreach(range(0, $files) as $index) {
            $rules['files.' . $index] = 'mimes:pdf|max:20000';
        }

        return $rules;
    }



}
