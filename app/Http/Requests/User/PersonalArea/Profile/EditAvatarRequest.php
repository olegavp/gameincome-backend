<?php

namespace App\Http\Requests\User\PersonalArea\Profile;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;


class EditAvatarRequest extends FormRequest
{
    #[ArrayShape(['avatar' => "string"])] public function rules(): array
    {
        return [
            'avatar' => 'bail|required|file|image|max:2500'
        ];
    }
}
