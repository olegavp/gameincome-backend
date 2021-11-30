<?php

namespace App\Http\Requests\User\PersonalArea\Purchases;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *      type="object",
 *      required={"text"}
 * )
 */
class CreateDisputeRequest extends FormRequest
{
    /**
     * @OA\Property(property="text", type="string"),
     * @OA\Property(property="image", type="string"),
     */

    #[ArrayShape(['text' => "string", 'image' => "string"])]
    public function rules(): array
    {
        return [
            'text' => 'bail|required|string',
            'image' => 'bail|file|image|max:2500'
        ];
    }
}
