<?php

namespace App\Http\Resources\Reviews;

use Illuminate\Http\Resources\Json\JsonResource;


class ReviewsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'itemName' => $this->item->name,
            'itemRate' => $this->item->metacritic,
            'itemBackground' => $this->item->header_image,
            'itemDeveloper' => $this->item->developer,
            'adminName' => $this->writer->name,
            'adminImage' => 'https://admin-salon.nethouse.ru/static/img/0000/0007/0436/70436824.9dfdgyefpu.W665.jpg',
            'reviewCommentsCount' => $this->comments->count(),
            'reviewViewsCount' => $this->views->count
        ];
    }
}
