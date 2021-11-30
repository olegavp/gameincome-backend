<?php

namespace App\Http\Resources\User\Purchase;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;


/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="PurchaseResource"
 *     )
 * )
 */
class PurchaseResource extends JsonResource
{

    /**
     * @OA\Property (property="data", type="array",
     *     @OA\Items(
     * @OA\Property(property="purchaseId", type="string"),
     * @OA\Property(property="keyId", type="string"),
     * @OA\Property(property="key", type="string"),
     * @OA\Property(property="itemName", type="string"),
     * @OA\Property(property="itemBackground", type="string"),
     * @OA\Property(property="itemPlatform", type="string"),
     * @OA\Property(property="createdAt", type="string"),
     *     ),
     *)
     * @OA\Property (property="status", type="integer", example="200")
     */

    #[ArrayShape(['purchaseId' => "mixed", 'keyId' => "mixed", 'key' => "mixed", 'itemName' => "mixed", 'itemBackground' => "mixed", 'itemPlatform' => "mixed", 'createdAt' => "mixed"])]
    public function toArray($request): array
    {
        $key = $this->key;
        $item = $this->item;
        $platforms = $item->platforms->map->name;

        return [
            'purchaseId' => $this->id,
            'keyId' => $key->id,
            'key' => $key->key,
            'itemName' => $item->name,
            'itemBackground' => $item->header_image,
            'itemPlatform' => $platforms,
            'createdAt' => $this->created_at,
        ];
    }
}
