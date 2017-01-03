<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required|email|unique:profiles',
            'role'          => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }
}