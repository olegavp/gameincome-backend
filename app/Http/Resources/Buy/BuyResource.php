<?php

namespace App\Http\Resources\Buy;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="LoginResource"
 *     )
 * )
 */
class BuyResource extends JsonResource
{

    /**
     * @OA\Property (property="data", type="array",
     *     @OA\Items(
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="background", type="string"),
     * @OA\Property(property="sellerNickname", type="string"),
     * @OA\Property(property="key", type="string"),
     * @OA\Property(property="price", type="number"),
     *     )
     *)
     * @OA\Property (property="status", type="integer", example="200")
     */
    #[ArrayShape(['name' => "mixed", 'background' => "mixed", 'sellerNickname' => "mixed", 'key' => "mixed", 'price' => "mixed"])]
    public function toArray($request): array
    {
        if ($this->service_sale_price !== null)
        {
            $price = $this->service_sale_price;
        }
        else
        {
            $price = $this->service_price;
        }

        return [
            'name' => $this->item->name,
            'background' => $this->item->header_image,
            'sellerNickname' => $this->seller->user->nickname,
            'key' => $this->key,
            'price' => $price
        ];
    }
}
