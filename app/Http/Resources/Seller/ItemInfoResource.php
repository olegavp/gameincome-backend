<?php

namespace App\Http\Resources\Seller;

use App\Models\Item\Region;
use Illuminate\Http\Resources\Json\JsonResource;


class ItemInfoResource extends JsonResource
{
    public function toArray($request)
    {
        $services = $this->services;
        $servicesString = null;
        foreach ($services as $service)
        {
            $servicesString = $servicesString .', '. $service->name;
        }
        $servicesString = substr($servicesString, 2);

        $platforms = $this->platforms;
        $platformsString = null;
        foreach ($platforms as $platform)
        {
            $platformsString = $platformsString .', '. $platform->name;
        }
        $platformsString = substr($platformsString, 2);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'developer' => $this->developer,
            'publisher' => $this->publisher,
            'detailedDescription' => $this->detailed_description,
            'metacritic' => $this->metacritic,
            'releaseDate' => $this->release_date,
            'linkToMedia' => $this->link_to_media,
            'pcRequirements' => $this->pc_requirements,
            'headerImage' => $this->header_image,
            'regions' => Region::query()->select('id', 'name')->get(),
            'service' => $servicesString,
            'platforms' => $platformsString
        ];
    }
}
