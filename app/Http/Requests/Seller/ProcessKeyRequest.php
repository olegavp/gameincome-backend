<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;


class ProcessKeyRequest extends FormRequest
{
    #[ArrayShape(['file' => "string"])]
    public function rules(): array
    {
        return [
            'file' => 'bail|required|file|max:1000|mimetypes:text/plain'
        ];
    }
}
