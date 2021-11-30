<?php

namespace App\Http\Requests\Items;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;


class FilterRequest extends FormRequest
{
    #[ArrayShape(['minPrice' => "string", 'maxPrice' => "string", 'categories' => "string", 'services' => "string", 'platforms' => "string", 'genres' => "string", 'itemType' => "string"])]
    public function rules(): array
    {
        return [
            'minPrice' => 'bail|required|string',
            'maxPrice' => 'bail|required|string',
            'categories' => 'bail|string',
            'services'  => 'bail|string',
            'platforms' => 'bail|string',
            'genres' => 'bail|string',
            'itemType' => 'bail|required|string',
        ];
    }
}
