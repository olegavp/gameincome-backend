<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;


class AddKeyRequest extends FormRequest
{
    #[ArrayShape(['itemId' => "string", 'regionId' => "string", 'price' => "string", 'keys' => "string", 'itemType' => "string"])]
    public function rules(): array
    {
        return [
            'itemId' => 'bail|required|string',
            'regionId' => 'bail|required|string',
            'price' => 'bail|required|numeric|between:100,100000000',
            'keys' => 'bail|required|string|min:4',
            'itemType' => 'bail|required|string',
        ];
    }
}
