<?php

namespace App\Http\Requests\Authorization;

use Illuminate\Foundation\Http\FormRequest;

class SendCodeRegistrationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|bail|string|max:100|',
            'surname' => 'required|bail|string|max:100|',
            'email' => 'required|bail|email|max:255|unique:users',
            'password' => 'required|bail|string|max:255|'
        ];
    }
}
