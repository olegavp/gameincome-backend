<?php

namespace App\Http\Resources\User\Sales\Software;

use Illuminate\Http\Resources\Json\JsonResource;


class SaleSoftwareResource extends JsonResource
{
    public function toArray($request): array
    {
        $itemInfo = $this->item;
        $services = $itemInfo->services;
        $servicesString = null;
        foreach ($services as $service)
        {
            $servicesString = $servicesString .', '. $service->name;
        }
        $servicesString = substr($servicesString, 2);

        $platforms = $itemInfo->platforms;
        $platformsString = null;
        foreach ($platforms as $platform)
        {
            $platformsString = $platformsString .', '. $platform->name;
        }
        $platformsString = substr($platformsString, 2);

        if ($this->service_sale_price != null)
        {
            $salePrice = $this->service_sale_price / 100;
            $price = $this->service_price / 100;
            $sale = round((($price - $salePrice) * 100) / $price);
        }
        else
        {
            $price = $this->service_price;
            $salePrice = null;
            $sale = null;
        }

        return [
            'itemId' => $itemInfo->id,
            'itemName' => $itemInfo->name,
            'itemService' => $servicesString,
            'platformId' => $platformsString,
            'itemHeaderImage' => $itemInfo->header_image,
            'oldPrice' => $price,
            'newPrice' => $salePrice,
            'sale' => $sale,
            'key_id' => $this->id,
            'itemCreatedAt' => $this->created_at
        ];
    }
}
