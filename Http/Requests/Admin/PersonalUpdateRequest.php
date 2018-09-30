<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PersonalUpdateRequest extends FormRequest
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
            'name' => 'max:255',
            'surname' => 'max:255',
            'email' => 'required|email|max:255',
            'speciality' => 'max:255',
            'job' => 'max:255',
            'avatar' => 'image|max:1024',
            'password' => 'max:255',
        ];
    }
}
