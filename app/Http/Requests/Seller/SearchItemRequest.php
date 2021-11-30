<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class SearchItemRequest extends FormRequest
{
    #[ArrayShape(['name' => "string"])]
    public function rules(): array
    {
        return [
            'name' => 'bail|required|string|max:255'
        ];
    }
}
