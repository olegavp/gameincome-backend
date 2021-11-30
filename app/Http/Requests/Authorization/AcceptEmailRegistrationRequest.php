<?php

namespace App\Http\Requests\Authorization;

use Illuminate\Foundation\Http\FormRequest;

class AcceptEmailRegistrationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'hash' => 'required|bail|string|max:255|',
            'code' => 'required|bail|digits:6'
        ];
    }
}
