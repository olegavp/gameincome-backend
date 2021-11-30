<?php

namespace App\Http\Resources\User\Sales;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="LoginResource"
 *     )
 * )
 */
class SaleResource extends JsonResource
{

    /**
     * @OA\Property (property="data", type="array",
     *     @OA\Items(
     * @OA\Property(property="itemId", type="string"),
     * @OA\Property(property="itemName", type="string"),
     * @OA\Property(property="itemService", type="string"),
     * @OA\Property(property="platformId", type="string"),
     * @OA\Property(property="itemHeaderImage", type="string"),
     * @OA\Property(property="oldPrice", type="number"),
     * @OA\Property(property="newPrice", type="number"),
     * @OA\Property(property="sale", type="number"),
     * @OA\Property(property="keyId", type="string"),
     * @OA\Property(property="itemCreatedAt", type="string"),
     *     ),
     *)
     * @OA\Property (property="status", type="integer", example="200")
     */
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
            'keyId' => $this->id,
            'itemCreatedAt' => $this->created_at
        ];

    }
}
