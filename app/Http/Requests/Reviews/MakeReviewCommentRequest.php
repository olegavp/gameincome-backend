<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;


class MakeReviewCommentRequest extends FormRequest
{
    #[ArrayShape(['reviewId' => "string", 'parentId' => "string", 'text' => "string"])]
    public function rules(): array
    {
        return [
            'reviewId' => 'bail|required|string',
            'parentId' => 'bail|string',
            'text' => 'bail|required|string'
        ];
    }
}
