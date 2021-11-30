<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;


class SellerFeedbackRequest extends FormRequest
{
    #[ArrayShape(['sellerId' => "string", 'keyId' => "string", 'itemType' => "string", 'rate' => "string", 'comment' => "string"])]
    public function rules(): array
    {
        return [
            'sellerId' => 'required|bail|string',
            'keyId' => 'required|bail|string',
            'itemType' => 'required|bail|string',
            'rate' => 'required|bail|in:1,0',
            'comment' => 'required|bail|string'
        ];
    }
}
