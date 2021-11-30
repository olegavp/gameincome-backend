<?php

namespace App\Http\Controllers\Seller\PublicProfile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\PublicProfile\SellerItemsResource;
use App\Models\Item\Game;
use App\Models\Item\GameKey;
use App\Models\Item\Software;
use App\Models\Item\SoftwareKey;
use App\Models\Seller\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class GetSellersItemsController extends Controller
{
    public function get(Request $request, Seller $seller): JsonResponse
    {
        $sort = ($request['sort']) ? $request['sort'] : 'desc';
        try
        {
            $games = GameKey::query()
                ->where(['seller_id' => $seller->id, 'bought' => 0])
                ->select('item_id')
                ->distinct('item_id')
                ->pluck('item_id');

            if($games->isNotEmpty())
            {
                $games = Game::query()
                    ->with('services', 'platforms')
                    ->whereIn('id', $games)
                    ->select('id', 'name', 'header_image', 'metacritic')
                    ->orderBy('metacritic', $sort)
                    ->get();

                $games->map(function ($value) {
                    $services = $value->services;
                    $servicesString = null;
                    foreach ($services as $service) {
                        $servicesString = $servicesString . ', ' . $service->name;
                    }
                    $servicesString = substr($servicesString, 2);

                    $platforms = $value->platforms;
                    $platformsString = null;
                    foreach ($platforms as $platform) {
                        $platformsString = $platformsString . ', ' . $platform->name;
                    }
                    $platformsString = substr($platformsString, 2);

                    $value->name = $value->name . ' (' . $servicesString . ') (' . $platformsString . ')';
                });
            }


            $software = SoftwareKey::query()
                ->where(['seller_id' => $seller->id, 'bought' => 0])
                ->select('item_id')
                ->distinct('item_id')
                ->pluck('item_id');

            if ($software->isNotEmpty())
            {
                $software = Software::query()
                    ->with('services', 'platforms')
                    ->whereIn('id', $software)
                    ->select('id', 'name', 'header_image', 'metacritic')
                    ->orderBy('metacritic', $sort)
                    ->get();

                $software->map(function ($value) {
                    $services = $value->services;
                    $servicesString = null;
                    foreach ($services as $service) {
                        $servicesString = $servicesString . ', ' . $service->name;
                    }
                    $servicesString = substr($servicesString, 2);

                    $platforms = $value->platforms;
                    $platformsString = null;
                    foreach ($platforms as $platform) {
                        $platformsString = $platformsString . ', ' . $platform->name;
                    }
                    $platformsString = substr($platformsString, 2);

                    $value->name = $value->name . ' (' . $servicesString . ') (' . $platformsString . ')';
                });
            }

            $games = SellerItemsResource::collection($games);
            $software = SellerItemsResource::collection($software);
            return response()->json(['games' => $games, 'soft' => $software, 'status' => 200], 200);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время загрузки игр продавца. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
