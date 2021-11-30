<?php

namespace App\Http\Requests\Buy;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *      type="object",
 * )
 */
class BuyRequest extends FormRequest
{
    /**
     * @OA\Property(property="items", type="array",
     *       @OA\Items(
     *           @OA\Property(property="keyId", type="string"),
     *           @OA\Property(property="itemType", type="string"),
     *       ),
     * ),
     * @OA\Property(property="paymentMethod", type="string", description="parameters: balance, card"),
     */
    #[ArrayShape(['items' => "string", 'paymentMethod' => "string", 'paymentAmount' => "integer"])]
    public function rules(): array
    {
        return [
            'items' => 'bail|required|array',
            'paymentMethod' => 'bail|required',
        ];
    }
}
