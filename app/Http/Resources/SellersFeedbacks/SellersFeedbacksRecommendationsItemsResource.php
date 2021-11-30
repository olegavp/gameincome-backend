<?php

namespace App\Http\Resources\SellersFeedbacks;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class SellersFeedbacksRecommendationsItemsResource extends JsonResource
{
    #[ArrayShape(['itemId' => "mixed", 'itemType' => "mixed|string", 'itemHeaderImage' => "mixed"])]
    public function toArray($request): array
    {
        if ($this->item_type === 'game')
        {
            $item = $this->games;
        }
        if ($this->item_type === 'software')
        {
            $item = $this->software;
        }

        return [
            'itemId' => $this->item_id,
            'itemType' => $this->item_type,
            'itemHeaderImage' => $item->map->header_image
        ];
    }
}
