<?php

namespace App\Http\Resources\SellersFeedbacks;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class ToSellerFeedbackMoreFeedbacksResource extends JsonResource
{
    #[ArrayShape(['id' => "mixed", 'itemName' => "mixed", 'itemHeaderImage' => "mixed", 'userAvatar' => "mixed", 'userNickname' => "mixed", 'userFeedbackText' => "mixed"])]
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
            'id' => $this->id,
            'itemName' => $item->name,
            'itemHeaderImage' => $item->header_image,
            'userAvatar' => $this->user->avatar,
            'userNickname' => $this->user->nickname,
            'userFeedbackText' => $this->comment
        ];
    }
}
