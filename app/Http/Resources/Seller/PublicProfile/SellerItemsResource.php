<?php

namespace App\Http\Resources\Seller\PublicProfile;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class SellerItemsResource extends JsonResource
{
    #[ArrayShape(['name' => "mixed", 'background' => "mixed", 'rate' => "mixed"])]
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'background' => $this->header_image,
            'rate' => $this->metacritic
        ];
    }
}
