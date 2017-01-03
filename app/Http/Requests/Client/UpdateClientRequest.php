<?php
namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:clients,email,' . $this->route('client'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}