<?php

namespace App\Http\Requests\Authorization;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *      type="object",
 *      required={"email", "password"}
 * )
 */
class LoginRequest extends FormRequest
{

    /**
     * @OA\Property(type="string", example="mail@example.com", property="email"),
     * @OA\Property(type="string", example="password", property="password"),
     */

    #[ArrayShape(['email' => "string", 'password' => "string"])]
    public function rules(): array
    {
        return [
            'email' => 'bail|required|email|max:255',
            'password' => 'bail|required|string|min:8|max:255',
        ];
    }
}
