<?php
namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function rules()
    {
        return [
            'client'                => 'required',
            'event_date'            => 'required',
            'start_times.0.time'    => 'required',
            'number_staff'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'start_times.0.time.required' => 'The start time is required.',
        ];
    }

    public function authorize()
    {
        return true;
    }
}