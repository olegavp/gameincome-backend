<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class InfoAboutItemWhichToCartResource extends JsonResource
{
    #[ArrayShape(['sellerId' => "mixed", 'sellerNickname' => "mixed", 'itemName' => "mixed", 'itemBackground' => "mixed", 'itemType' => "string", 'itemPrice' => "float|int", 'itemSalePrice' => "float|int|null", 'itemSale' => "float|null"])]
    public function toArray($request): array
    {
        $itemInfo = $this->item;
        $sellerInfo = $this->seller;

        $oldPrice = $this->service_price / 100;
        $newPrice = null;
        $sale = null;
        if ($this->service_sale_price !== null)
        {
            $newPrice = $this->service_sale_price / 100;
            $sale = round((($oldPrice - $newPrice) * 100) / $oldPrice);
        }

        if (get_class($this->resource) === 'App\Models\Item\GameKey')
        {
            $itemType = 'game';
        }
        else
        {
            $itemType = 'software';
        }

        return [
            'sellerId' => $sellerInfo->id,
            'sellerNickname' => $sellerInfo->user->nickname,
            'itemName' => $itemInfo->name,
            'itemBackground' => $itemInfo->header_image,
            'itemType' => $itemType,
            'itemPrice' => $oldPrice,
            'itemSalePrice' => $newPrice,
            'itemSale' => $sale,
        ];
    }
}
