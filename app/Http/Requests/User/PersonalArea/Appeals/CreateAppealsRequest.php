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
class CreateAppealsRequest extends FormRequest
{
    /**
     * @OA\Property(property="theme", type="string"),
     * @OA\Property(property="text", type="string"),
     * @OA\Property(property="itemType", type="string",example=null , description="Only for disputes: games / software / swiches / cases"),
     * @OA\Property(property="keyId", type="string", example=null, description="Only for disputes"),
     * @OA\Property(property="image", type="image", example=null, description="Only for disputes not required"),
     */

    #[ArrayShape(['theme' => "string", 'text' => "string", 'itemType' => "string", 'keyId' => "string", 'image' => "file"])]
    public function rules(): array
    {
        return [
            'theme' => 'bail|nullable|string',
            'text' => 'bail|required|string',
            'itemType' => 'bail|nullable|string',
            'keyId' => 'bail|nullable|string',
            'image' => 'bail|nullable|file',
        ];
    }
}
