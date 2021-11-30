<?php

namespace App\Http\Requests\Once;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;


class PromoCodeRequest extends FormRequest
{
    #[ArrayShape(['name' => "string"])]
    public function rules(): array
    {
        return [
            'name' => 'bail|required|string|min:3|max:50'
        ];
    }
}
