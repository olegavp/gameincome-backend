<?php

namespace App\Http\Requests\User\PersonalArea\Profile;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class EditInfoRequest extends FormRequest
{
    #[ArrayShape(['name' => "string", 'surname' => "string", 'nickname' => "string"])]
    public function rules(): array
    {
        return [
            'name' => 'required|bail|string|max:100|',
            'surname' => 'required|bail|string|max:100|',
            'nickname' => 'required|bail|string|min:2|max:100'
        ];
    }
}
