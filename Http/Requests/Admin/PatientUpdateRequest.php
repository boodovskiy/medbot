<?php

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PatientUpdateRequest extends FormRequest
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
        $tomorrow = Carbon::now()->addDays(1)->toDateString();
        return [
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
            'birth' => 'date|before:'.$tomorrow,
            'death' => 'date|after:birth',
            'gender_id' => 'required',
            'email' => 'email|max:255',
            'phone' => 'required|regex:/[0-9]{4,10}/',
            'additional' => 'max:1500'
        ];
    }
}
