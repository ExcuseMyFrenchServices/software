<?php
namespace App\Http\Requests\Event;

use App\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TimesheetRequest extends FormRequest
{
    public function rules()
    {
        return [];
    }

    public function authorize()
    {
        $event = Event::find($this->route('event'));

        return Auth::user()->role_id == 1 || Auth::user()->id == $event->admin_id;
    }
}