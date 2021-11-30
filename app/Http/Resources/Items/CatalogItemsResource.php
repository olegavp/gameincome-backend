<?php

namespace App\Http\Resources\Items;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class CatalogItemsResource extends JsonResource
{
    #[ArrayShape(['itemId' => "mixed", 'itemName' => "mixed", 'itemDeveloper' => "mixed", 'itemMetacritic' => "mixed", 'itemBackground' => "mixed", 'itemPrice' => "array"])]
    public function toArray($request): array
    {
        $salePrice = null;
        $sale = null;

        if ($this->service_sale_price != null)
        {
            $salePrice = $this->service_sale_price / 100;
            $oldPrice = $this->service_price / 100;
            $sale = round((($oldPrice - $salePrice) * 100) / $oldPrice);
        }
        else
        {
            $oldPrice = $this->service_price / 100;
        }

        return [
            'itemId' => $this->item_id,
            'itemName' => $this->item->name,
            'itemDeveloper' => $this->item->developer,
            'itemMetacritic' => $this->item->metacritic,
            'itemBackground' => $this->item->header_image,
            'itemPrice' => ['old' => $oldPrice, 'new' => $salePrice, 'sale' => $sale]
        ];
    }
}
