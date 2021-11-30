<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\MainPage\RecommendationsResource;
use App\Models\Item\Game;
use App\Models\Item\GameKey;
use App\Models\Item\Software;
use App\Models\Item\SoftwareKey;
use App\Models\MainPage\Recommendation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class GetRecommendationsInCartController extends Controller
{
    public function getRecommendations(): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            $recommendations = Recommendation::query()->get();

            $games = array();
            $software = array();
            foreach ($recommendations as $recommendation)
            {
                if ($recommendation->item_type === 'game')
                {
                    $games[$recommendation->id]['id'] = $recommendation->id;
                    $games[$recommendation->id]['item_id'] = $recommendation->item_id;
                    $games[$recommendation->id]['item_category'] = 'recommendations';
                }
                elseif ($recommendation->item_type === 'software')
                {
                    $software[$recommendation->id]['id'] = $recommendation->id;
                    $software[$recommendation->id]['item_id'] = $recommendation->item_id;
                    $software[$recommendation->id]['item_category'] = 'recommendations';
                }
                else
                {
                    throw new \Exception();
                }
            }

            $gameKeys = null;
            $softwareKeys = null;
            if (count($games) > 0)
            {
                $gameId = collect($games)->pluck('item_id');
                $gamesInfo = Game::query()->whereIn('id', $gameId)->get();
                $gameKeys = GameKey::query()->where('bought', 0)->whereIn('item_id', $gameId)->get();
            }
            elseif (count($software) > 0)
            {
                $softwareId = collect($software)->pluck('item_id');
                $softwareInfo = Software::query()->whereIn('id', $softwareId)->get();
                $softwareKeys = SoftwareKey::query()->where('bought', 0)->whereIn('item_id', $softwareInfo)->get();
            }

            $recommendations = array();
            foreach ($games as $game)
            {
                if ($game['item_category'] === 'recommendations')
                {
                    $item = array_values($gamesInfo->where('id', $game['item_id'])->toArray())[0];
                    array_push($recommendations, $item);
                }
            }
            foreach ($software as $oneSoftware)
            {
                if ($oneSoftware['item_category'] === 'recommendations')
                {
                    $item = array_values($gamesInfo->where('id', $oneSoftware['item_id'])->toArray())[0];
                    array_push($recommendations, $item);
                }
            }

            $recommendations = collect(json_decode(json_encode($recommendations), FALSE));
            $recommendations->map(function ($value) use ($gameKeys, $softwareKeys)
            {
                if ($gameKeys === null)
                {
                    $itemKeys = $softwareKeys->where('item_id', $value->id)->where('service_price', $softwareKeys->where('item_id',  $value->id)->min('service_price'));
                }
                else
                {
                    $itemKeys = $gameKeys->where('item_id', $value->id)->where('service_price', $gameKeys->where('item_id',  $value->id)->min('service_price'));
                }

                if (isset($itemKeys->pluck('service_price')[0]))
                {
                    $value->oldPrice = $itemKeys->pluck('service_price')[0];

                    if ($itemKeys->pluck('service_sale_price') !== null)
                    {
                        $value->newPrice = $itemKeys->pluck('service_sale_price')[0];
                    }
                    else
                    {
                        $value->newPrice = null;
                    }
                }
                else
                {
                    $value->oldPrice = null;
                    $value->newPrice = null;
                }
            });

            return (RecommendationsResource::collection($recommendations))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при загрузке товаров предложений в корзине. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
