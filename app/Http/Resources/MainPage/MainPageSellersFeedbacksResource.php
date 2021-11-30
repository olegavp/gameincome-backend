<?php

namespace App\Http\Resources\MainPage;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class MainPageSellersFeedbacksResource extends JsonResource
{
    #[ArrayShape(['id' => "mixed", 'avatar' => "mixed", 'nickname' => "mixed", 'itemHeaderImage' => "mixed", 'itemName' => "mixed", 'itemRating' => "mixed", 'comment' => "mixed", 'createdAt' => "mixed"])]
    public function toArray($request): array
    {
        $user = $this->user;

        if (isset($this->gameKey))
        {
            $item = $this->gameKey->item;
        }
        else
        {
            $item = $this->softwareKey->item;
        }

        return [
            'id' => $this->id,
            'avatar' => $user->avatar,
            'nickname' => $user->nickname,
            'itemHeaderImage' => $item->header_image,
            'itemName' => $item->name,
            'itemRating' => $item->metacritic,
            'comment' => $this->comment,
            'createdAt' => $this->created_at
        ];
    }
}
