<?php

namespace App\Http\Services\Search;

use App\Http\Resources\Search\SearchResource;
use App\Models\Item\Game;
use App\Models\Item\Software;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class SearchItemsService
{
    public function search($request): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            $gamesItems = Game::query()->where('name', 'like', $request->name . '%')->with('services', 'platforms')->select('id', 'name', 'header_image')->get();
            $softwareItems = Software::query()->where('name', 'like', $request->name . '%')->with('services', 'platforms')->select('id', 'name', 'header_image')->get();
            if ($gamesItems->isEmpty() and $softwareItems->isEmpty())
            {
                return response()->json(['items' => [], 'status' => 200], 200);
            }

            if ($gamesItems->isNotEmpty())
            {
                $gamesItems->map(function ($value)
                {
                    $services = $value->services;
                    $servicesString = null;
                    foreach ($services as $service)
                    {
                        $servicesString = $servicesString .', '. $service->name;
                    }
                    $servicesString = substr($servicesString, 2);

                    $platforms = $value->platforms;
                    $platformsString = null;
                    foreach ($platforms as $platform)
                    {
                        $platformsString = $platformsString .', '. $platform->name;
                    }
                    $platformsString = substr($platformsString, 2);

                    $value->name = $value->name . ' (' . $servicesString . ') (' . $platformsString . ')';

                    $value->itemType = 'game';
                });
            }

            if ($softwareItems->isNotEmpty())
            {
                $softwareItems->map(function ($value)
                {
                    $services = $value->services;
                    $servicesString = null;
                    foreach ($services as $service)
                    {
                        $servicesString = $servicesString .', '. $service->name;
                    }
                    $servicesString = substr($servicesString, 2);

                    $platforms = $value->platforms;
                    $platformsString = null;
                    foreach ($platforms as $platform)
                    {
                        $platformsString = $platformsString .', '. $platform->name;
                    }
                    $platformsString = substr($platformsString, 2);

                    $value->name = $value->name . ' (' . $servicesString . ') (' . $platformsString . ')';

                    $value->itemType = 'software';
                });
            }
            $items = $gamesItems->merge($softwareItems);

            return SearchResource::collection($items)
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отображения результатов поиска. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
