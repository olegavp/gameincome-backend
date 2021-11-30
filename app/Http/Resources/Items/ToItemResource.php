<?php

namespace App\Http\Resources\Items;

use Illuminate\Http\Resources\Json\JsonResource;


class ToItemResource extends JsonResource
{
    public function toArray($request): array
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

        if (!isset($this->seller))
        {
            return [
                'name' => $this->name,
                'developer' => $this->developer,
                'publisher' => $this->publisher,
                'shortDescription' => $this->short_description,
                'detailedDescription' => $this->detailed_description,
                'metacritic' => $this->metacritic,
                'releaseDate' => $this->release_date,
                'linkToMedia' => $this->link_to_media,
                'pcRequirements' => $this->pc_requirements,
                'headerImage' => $this->header_image,
                'service' => $servicesString,
                'platforms' => $platformsString,
                'sellerId' => null,
                'sellerAvatar' => null,
                'sellerNickname' => null,
                'sellerName' => null,
                'sellerSurname' => null,
                'likes' => null,
                'dislikes' => null,
                'feedbacks' => null,
                'keyId' => null,
                'itemType' => $this->item_type,
                'itemPrice' => ['old' => null, 'new' => null, 'sale' => null]
            ];
        }

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

        $sellerInfo = $this->seller;
        $sellerFeedbacks = $this->seller->feedbacks;
        $likes = collect($sellerFeedbacks)->where('rate', 1)->count();
        $dislikes = collect($sellerFeedbacks)->where('rate', 0)->count();
        $feedbacks = array();
        foreach ($sellerFeedbacks as $key => $feedback)
        {
            $feedbacks[$key]['nickname'] = $feedback->user->nickname;
            $feedbacks[$key]['avatar'] = $feedback->user->avatar;
            $feedbacks[$key]['comment'] = $feedback->comment;
            $feedbacks[$key]['time'] = $feedback->created_at;
        }

        return [
            'name' => $this->name,
            'developer' => $this->developer,
            'publisher' => $this->publisher,
            'shortDescription' => $this->short_description,
            'detailedDescription' => $this->detailed_description,
            'metacritic' => $this->metacritic,
            'releaseDate' => $this->release_date,
            'linkToMedia' => $this->link_to_media,
            'pcRequirements' => $this->pc_requirements,
            'headerImage' => $this->header_image,
            'service' => $servicesString,
            'platforms' => $platformsString,
            'sellerId' => $sellerInfo->id,
            'sellerAvatar' => $sellerInfo->user->avatar,
            'sellerNickname' => $sellerInfo->user->nickname,
            'sellerName' => $sellerInfo->user->name,
            'sellerSurname' => $sellerInfo->user->surname,
            'likes' => $likes,
            'dislikes' => $dislikes,
            'feedbacks' => $feedbacks,
            'keyId' => $this->id,
            'itemType' => $this->item_type,
            'itemPrice' => ['old' => $price, 'new' => $salePrice, 'sale' => $sale]
        ];
    }
}
