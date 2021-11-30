<?php

namespace App\Http\Requests\User\PersonalArea\Appeals;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *      type="object",
 *      required={"theme", "text"}
 * )
 */
class CreateMessageAppealsRequest extends FormRequest
{

    /**
     * @OA\Property(property="text", type="string"),
     */
    #[ArrayShape(['text' => "string"])]
    public function rules(): array
    {
        return [
            'text' => 'bail|required|string'
        ];
    }
}
