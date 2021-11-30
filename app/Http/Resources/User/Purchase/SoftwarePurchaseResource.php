<?php

namespace App\Http\Resources\User\Purchase;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class SoftwarePurchaseResource extends JsonResource
{
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
