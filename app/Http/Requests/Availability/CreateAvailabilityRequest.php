<?php
namespace App\Http\Requests\Availability;

use Illuminate\Foundation\Http\FormRequest;

class CreateAvailabilityRequest extends FormRequest
{
    public function rules()
    {
        return [
            'dates.0.date'  => 'required',
        ];
    }

    public function messages()
    {
        return [
            'dates.0.date.required' => 'At least one availability is required.',
        ];
    }

    public function authorize()
    {
        return true;
    }
}