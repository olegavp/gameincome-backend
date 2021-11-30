<?php

namespace App\Http\Resources\SellersFeedbacks;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class ToSellerFeedbackResource extends JsonResource
{
    #[ArrayShape(['itemName' => "mixed", 'itemBackground' => "mixed", 'itemRate' => "mixed", 'userAvatar' => "mixed", 'userNickname' => "mixed", 'userFeedbackText' => "mixed"])]
    public function toArray($request): array
    {
        if ($this->item_type === 'game')
        {
            $item = $this->gameKey->item;
        }

        if ($this->item_type === 'software')
        {
            $item = $this->softwareKey->item;
        }

        return [
            'itemName' => $item->name,
            'itemBackground' => $item->header_image,
            'itemRate' => $item->metacritic,
            'userAvatar' => $this->user->avatar,
            'userNickname' => $this->user->nickname,
            'userFeedbackText' => $this->comment
        ];
    }
}
