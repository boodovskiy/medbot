<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentUpdateRequest extends FormRequest
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
            'eventMarker' => 'required',
            'start' => 'required|date',
            'periodicity' => 'required|min:1|max:2|integer',
            'length' => 'required|min:1|max:30|integer',
            'text' => 'required|max:255',
        ];
    }
}
