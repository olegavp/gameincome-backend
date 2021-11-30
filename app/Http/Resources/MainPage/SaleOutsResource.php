<?php

namespace App\Http\Resources\MainPage;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class SaleOutsResource extends JsonResource
{
    #[ArrayShape(['itemId' => "mixed", 'itemName' => "mixed", 'itemDeveloper' => "mixed", 'itemScore' => "mixed", 'itemBackground' => "mixed", 'itemType' => "mixed", 'itemPrice' => "array"])]
    public function toArray($request): array
    {
        if ($this->oldPrice !== null)
        {
            if ($this->newPrice !== null)
            {
                $salePrice = $this->newPrice / 100;
                $price = $this->oldPrice / 100;
                $sale = round((($price - $salePrice) * 100) / $price);
            }
            else
            {
                $price = $this->oldPrice / 100;
                $salePrice = null;
                $sale = null;
            }

            return [
                'itemId' => $this->id,
                'itemName' => $this->name,
                'itemDeveloper' => $this->developer,
                'itemScore' => $this->metacritic,
                'itemBackground' => $this->header_image,
                'itemType' => $this->item_type,
                'itemPrice' => ['old' => $price, 'new' => $salePrice, 'sale' => $sale]
            ];
        }

        return [
            'itemId' => $this->id,
            'itemName' => $this->name,
            'itemDeveloper' => $this->developer,
            'itemScore' => $this->metacritic,
            'itemBackground' => $this->header_image,
            'itemType' => $this->item_type,
            'itemPrice' => ['old' => $this->oldPrice, 'new' => null, 'sale' => null]
        ];
    }
}
