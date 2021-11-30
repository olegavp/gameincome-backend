<?php

namespace App\Http\Resources\User\Purchase;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;


/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="KeyPurchaseResource"
 *     )
 * )
 */
class KeyPurchaseResource extends JsonResource
{

    /**
     * @OA\Property (property="data", type="array",
     *     @OA\Items(
     * @OA\Property(property="itemName", type="string"),
     * @OA\Property(property="itemBackground", type="string"),
     * @OA\Property(property="price", type="number"),
     * @OA\Property(property="keyId", type="string"),
     * @OA\Property(property="key", type="string"),
     * @OA\Property(property="sellerId", type="string"),
     * @OA\Property(property="sellerName", type="string"),
     * @OA\Property(property="sellerSurname", type="string"),
     * @OA\Property(property="sellerNickname", type="string"),
     *     ),
     *)
     * @OA\Property (property="status", type="integer", example="200")
     */

    #[ArrayShape(['itemName' => "mixed", 'itemBackground' => "mixed", 'price' => "mixed", 'keyId' => "mixed", 'key' => "mixed", 'sellerId' => "mixed", 'sellerName' => "mixed", 'sellerSurname' => "mixed", 'sellerNickname' => "mixed"])]
    public function toArray($request): array
    {
        $item = $this->item;
        $seller = $this->seller;
        $sellerUser = $this->seller->user;
        $key = $this->key;
        if ($key->service_sale_price !== null)
        {
            $price = $key->service_sale_price;
        }
        else
        {
            $price = $key->service_price;
        }


        return [
            'itemName' => $item->name,
            'itemBackground' => $item->header_image,
            'price' => $price,
            'keyId' => $key->id,
            'key' => $key->key,
            'sellerId' => $seller->id,
            'sellerName' => $sellerUser->name,
            'sellerSurname' => $sellerUser->surname,
            'sellerNickname' => $sellerUser->nickname
        ];
    }
}
