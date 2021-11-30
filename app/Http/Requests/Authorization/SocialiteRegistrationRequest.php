<?php

namespace App\Http\Requests\Authorization;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class SocialiteRegistrationRequest extends FormRequest
{
    #[ArrayShape(['hash' => "string", 'nickname' => "string", 'password' => "string"])]
    public function rules(): array
    {
        return [
            'hash' => 'required|bail|string',
            'nickname' => 'required|bail|string|max:100|min:3',
            'password' => 'required|bail|string|max:255|min:8',
        ];
    }
}
