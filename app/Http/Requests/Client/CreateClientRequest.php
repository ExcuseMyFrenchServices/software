<?php
namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'          => 'required',
            'phone_number'  => 'required',
            'email'         => 'required|email|unique:clients',
        ];
    }

    public function authorize()
    {
        return true;
    }
}