<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;


class MakeCommentRequest extends FormRequest
{
    #[ArrayShape(['newsId' => "string", 'parentId' => "string", 'text' => "string"])]
    public function rules(): array
    {
        return [
            'newsId' => 'bail|required|string',
            'parentId' => 'bail|string',
            'text' => 'bail|required|string'
        ];
    }
}
