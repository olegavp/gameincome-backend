<?php

namespace App\Http\Requests\Once;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class EmailRequest extends FormRequest
{
    #[ArrayShape(['email' => "string"])]
    public function rules(): array
    {
        return [
            'email' => 'required|bail|email|max:255',
        ];
    }
}
