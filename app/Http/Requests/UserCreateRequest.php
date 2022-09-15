<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
            'name' => 'required|max:255|min:5',
            'email' => 'required|email|max:255',
            'username' => 'required|max:25|min:5|unique:users',
            'usertype'=>'required',
            'password'=>'required|min:5|max:25',
            'post'=>'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1082',
            'branch'=>'required',
        ];
    }
}
