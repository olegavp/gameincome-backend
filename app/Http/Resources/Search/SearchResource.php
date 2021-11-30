<?php

namespace App\Http\Resources\Search;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

class SearchResource extends JsonResource
{
    #[ArrayShape(['itemId' => "mixed", 'itemName' => "mixed", 'itemHeaderImage' => "mixed", 'itemType' => "mixed"])]
    public function toArray($request): array
    {
        return [
            'itemId' => $this->id,
            'itemName' => $this->name,
            'itemHeaderImage' => $this->header_image,
            'itemType' => $this->itemType
        ];
    }
}
