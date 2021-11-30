<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;


class ItemIdRequest extends FormRequest
{
    #[ArrayShape(['id' => "string", 'itemType' => "string"])]
    public function rules(): array
    {
        return [
            'id' => 'bail|required|string',
            'itemType' => 'bail|required|string'
        ];
    }
}
