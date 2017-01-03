<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'password'  => 'required',
            'confirm'   => 'required|same:password',
        ];
    }

    public function authorize()
    {
        return true;
    }
}