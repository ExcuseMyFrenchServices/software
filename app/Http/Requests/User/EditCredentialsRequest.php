<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class EditCredentialsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'username'  => 'required|unique:users,username,' . $this->route('user'),
            'confirm'   => 'required_with:password|same:password',
        ];
    }

    public function authorize()
    {
        return true;
    }
}