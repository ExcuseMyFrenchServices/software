<?php
namespace App\Http\Requests\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class SubmitFeedbackRequest extends FormRequest
{
    public function rules()
    {
        return [
            'rating'    => 'numeric|between:1,5',
            'comment'   => 'required|string',
        ];
    }

    public function authorize()
    {
        return true;
    }
}