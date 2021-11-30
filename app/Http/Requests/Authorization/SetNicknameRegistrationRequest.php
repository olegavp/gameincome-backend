<?php

namespace App\Http\Requests\Authorization;

use Illuminate\Foundation\Http\FormRequest;

class SetNicknameRegistrationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' =>'required|bail',
            'email' => 'required|bail|email|max:255',
            'nickname' => 'required|bail|string|min:2|max:100',
            'code' => 'required|bail|digits:6'
        ];
    }
}
