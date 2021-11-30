<?php

namespace App\Http\Requests\Once;

use Illuminate\Foundation\Http\FormRequest;

class NicknameRequest extends FormRequest
{
    public function rules()
    {
        return [
            'nickname' => 'required|bail|string|min:2|max:100'
        ];
    }
}
