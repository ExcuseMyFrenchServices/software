<?php
namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
{
    public function rules()
    {
        return [
            'client'                => 'required',
            'booking_date'          => 'required',
            'event_date'            => 'required'
        ];
    }

    public function authorize()
    {
        return true;
    }
}