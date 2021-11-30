<?php

namespace App\Http\Resources\Items;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class ToItemMoreSellersResource extends JsonResource
{
    #[ArrayShape(['sellerId' => "mixed", 'sellerAvatar' => "mixed", 'sellerNickname' => "mixed", 'sellerName' => "mixed", 'sellerSurname' => "mixed", 'likes' => "mixed", 'dislikes' => "mixed", 'keyId' => "mixed", 'itemPrice' => "array"])]
    public function toArray($request): array
    {
        $sellerInfo = $this->seller->user;
        $sellerFeedbacks = $this->seller->feedbacks;
        $likes = $sellerFeedbacks->where('rate', 1)->count();
        $dislikes = $sellerFeedbacks->where('rate', 0)->count();

        if ($this->service_sale_price !== null)
        {
            $salePrice = $this->service_sale_price / 100;
            $price = $this->service_price / 100;
            $sale = round((($price - $salePrice) * 100) / $price);
        }
        else
        {
            $price = $this->service_price / 100;
            $salePrice = null;
            $sale = null;
        }

        return [
            'sellerId' => $this->seller_id,
            'sellerAvatar' => $sellerInfo->avatar,
            'sellerNickname' => $sellerInfo->nickname,
            'sellerName' => $sellerInfo->name,
            'sellerSurname' => $sellerInfo->surname,
            'likes' => $likes,
            'dislikes' => $dislikes,
            'keyId' => $this->id,
            'itemPrice' => ['old' => $price, 'new' => $salePrice, 'sale' => $sale],
        ];
    }
}
