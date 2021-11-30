<?php

namespace App\Http\Resources\MainPage;

use App\Http\Resources\Reviews\ReviewsResource;
use App\Models\Item\Game;
use App\Models\Item\GameKey;
use App\Models\Item\Software;
use App\Models\Item\SoftwareKey;
use App\Models\MainPage\Category;
use App\Models\MainPage\Hit;
use App\Models\MainPage\Insert;
use App\Models\MainPage\Novelty;
use App\Models\MainPage\Recommendation;
use App\Models\MainPage\Review;
use App\Models\MainPage\SaleOut;
use App\Models\MainPage\Service;
use App\Models\MainPage\Slider;
use App\Models\Seller\SellerFeedback;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;


class MainPageResource extends JsonResource
{
    public function toArray($request): array
    {
        $games = array();
        $software = array();

        $recommendations = Recommendation::query()->get();
        $hits = Hit::query()->get();
        $novelties = Novelty::query()->get();
        $saleOuts = SaleOut::query()->get();

        foreach ($recommendations as $recommendation)
        {
            if ($recommendation->item_type === 'game')
            {
                $games[$recommendation->id . 1]['id'] = $recommendation->id;
                $games[$recommendation->id . 1]['item_id'] = $recommendation->item_id;
                $games[$recommendation->id . 1]['item_category'] = 'recommendations';
            }
            elseif ($recommendation->item_type === 'software')
            {
                $software[$recommendation->id . 1]['id'] = $recommendation->id;
                $software[$recommendation->id . 1]['item_id'] = $recommendation->item_id;
                $software[$recommendation->id . 1]['item_category'] = 'recommendations';
            }
            else
            {
                throw new \Exception();
            }
        }


        foreach ($hits as $hit)
        {
            if ($hit->item_type === 'game')
            {
                $games[$hit->id . 2]['id'] = $hit->id;
                $games[$hit->id . 2]['item_id'] = $hit->item_id;
                $games[$hit->id . 2]['item_category'] = 'hits';
            }
            elseif ($hit->item_type === 'software')
            {
                $software[$hit->id . 2]['id'] = $hit->id;
                $software[$hit->id . 2]['item_id'] = $hit->item_id;
                $software[$hit->id . 2]['item_category'] = 'hits';
            }
            else
            {
                throw new \Exception();
            }
        }


        foreach ($novelties as $novelty)
        {
            if ($novelty->item_type === 'game')
            {
                $games[$novelty->id . 3]['id'] = $novelty->id;
                $games[$novelty->id . 3]['item_id'] = $novelty->item_id;
                $games[$novelty->id . 3]['item_category'] = 'novelties';
            }
            elseif ($novelty->item_type === 'software')
            {
                $software[$novelty->id . 3]['id'] = $novelty->id;
                $software[$novelty->id . 3]['item_id'] = $novelty->item_id;
                $software[$novelty->id . 3]['item_category'] = 'novelties';
            }
            else
            {
                throw new \Exception();
            }
        }

        foreach ($saleOuts as $saleOut)
        {
            if ($saleOut->item_type === 'game')
            {
                $games[$saleOut->id . 4]['id'] = $saleOut->id;
                $games[$saleOut->id . 4]['item_id'] = $saleOut->item_id;
                $games[$saleOut->id . 4]['item_category'] = 'saleOuts';
            }
            elseif ($saleOut->item_type === 'software')
            {
                $software[$saleOut->id . 4]['id'] = $saleOut->id;
                $software[$saleOut->id . 4]['item_id'] = $saleOut->item_id;
                $software[$saleOut->id . 4]['item_category'] = 'saleOuts';
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
        $hits = array();
        $novelties = array();
        $saleOuts = array();

        foreach ($games as $game)
        {
            if ($game['item_category'] === 'recommendations')
            {
                $item = array_values($gamesInfo->where('id', $game['item_id'])->toArray())[0];
                array_push($recommendations, $item);
            }
            if ($game['item_category'] === 'hits')
            {
                $item = array_values($gamesInfo->where('id', $game['item_id'])->toArray())[0];
                array_push($hits, $item);
            }
            if ($game['item_category'] === 'novelties')
            {
                $item = array_values($gamesInfo->where('id', $game['item_id'])->toArray())[0];
                array_push($novelties, $item);
            }
            if ($game['item_category'] === 'saleOuts')
            {
                $item = array_values($gamesInfo->where('id', $game['item_id'])->toArray())[0];
                array_push($saleOuts, $item);
            }
        }
        foreach ($software as $oneSoftware)
        {
            if ($oneSoftware['item_category'] === 'recommendations')
            {
                $item = array_values($gamesInfo->where('id', $oneSoftware['item_id'])->toArray())[0];
                array_push($recommendations, $item);
            }
            if ($oneSoftware['item_category'] === 'hits')
            {
                $item = array_values($gamesInfo->where('id', $oneSoftware['item_id'])->toArray())[0];
                array_push($hits, $item);
            }
            if ($oneSoftware['item_category'] === 'novelties')
            {
                $item = array_values($gamesInfo->where('id', $oneSoftware['item_id'])->toArray())[0];
                array_push($novelties, $item);
            }
            if ($oneSoftware['item_category'] === 'saleOuts')
            {
                $item = array_values($gamesInfo->where('id', $oneSoftware['item_id'])->toArray())[0];
                array_push($saleOuts, $item);
            }
        }


        $recommendations = collect(json_decode(json_encode($recommendations), FALSE));
        $hits = collect(json_decode(json_encode($hits), FALSE));
        $novelties = collect(json_decode(json_encode($novelties), FALSE));
        $saleOuts = collect(json_decode(json_encode($saleOuts), FALSE));


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

        $hits->map(function ($value) use ($gameKeys, $softwareKeys)
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

        $novelties->map(function ($value) use ($gameKeys, $softwareKeys)
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

        $saleOuts->map(function ($value) use ($gameKeys, $softwareKeys)
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


        $reviews = \App\Models\Review\Review::query()->with('writer', 'comments', 'views')->get();

        $reviewsGames = array();
        $reviewsSoftware = array();
        foreach ($reviews as $review)
        {
            if ($review->item_type === 'game')
            {
                array_push($reviewsGames, $review->item_id);
            }
            elseif ($review->item_type === 'software')
            {
                array_push($reviewsSoftware, $review->item_id);
            }
        }

        if (count($reviewsGames) > 0)
        {
            $games = Game::query()->whereIn('id', $reviewsGames)->get();
            foreach ($reviews as $review)
            {
                foreach ($games as $game)
                {
                    if ($review->item_id === $game->id)
                    {
                        $review['item'] = $game;
                    }
                }
            }
        }
        elseif (count($reviewsSoftware) > 0)
        {
            $software = Software::query()->whereIn('id', $reviewsSoftware)->get();
            foreach ($reviews as $review)
            {
                foreach ($software as $oneSoftware)
                {
                    if ($review->item_id === $oneSoftware->id)
                    {
                        $review['item'] = $oneSoftware;
                    }
                }
            }
        }


        return [
            'slider' =>  SliderResource::collection(Slider::query()->get()),
            'inserts' => InsertResource::collection(Insert::query()->get()),
            'recommendations' => RecommendationsResource::collection($recommendations),
            'hits' => HitsResource::collection($hits),
            'novelties' => NoveltiesResource::collection($novelties),
            'saleOuts' => SaleOutsResource::collection($saleOuts),
            'categories' => CategoriesResource::collection(Category::query()->with('category')->get()),
            'sellersFeedbacks' => MainPageSellersFeedbacksResource::collection(SellerFeedback::query()->with('user', 'gameKey.item', 'softwareKey.item')->where('rate', 1)->orderBy(DB::raw('RAND()'))->take(5)->get()),
            'reviews' => ReviewsResource::collection($reviews),
            'services' => ServicesResource::collection(Service::query()->get())
        ];
    }
}
