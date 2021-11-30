<?php

namespace App\Http\Requests\User\PersonalArea\Security;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class PasswordRequest extends FormRequest
{
    #[ArrayShape(['password' => "string", 'newPassword' => "string"])]
    public function rules(): array
    {
        return [
            'password' => 'bail|required|string|min:8|max:255',
            'newPassword' => 'bail|required|string|min:8|max:255'
        ];
    }
}
