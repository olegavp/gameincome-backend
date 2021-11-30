<?php

namespace App\Http\Requests\Once;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class PasswordRequest extends FormRequest
{
    #[ArrayShape(['password' => "string"])]
    public function rules(): array
    {
        return [
            'password' => 'bail|required|string|min:8|max:255',
        ];
    }
}
