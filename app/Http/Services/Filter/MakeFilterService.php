<?php

namespace App\Http\Services\Filter;

use App\Models\Item\Category;
use App\Models\Item\GameKey;
use App\Models\Item\Genre;
use App\Models\Item\Platform;
use App\Models\Item\Service;
use App\Models\Item\SoftwareKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class MakeFilterService
{
    public function make($type): JsonResponse|array
    {
        try
        {
            if ($type === 'games')
            {
                $keys = GameKey::query()
                    ->where('bought', 0)
                    ->select('item_id', 'service_price', 'service_sale_price')
                    ->groupBy('item_id')
                    ->get();
            }
            if ($type === 'software')
            {
                $keys = SoftwareKey::query()
                    ->where('bought', 0)
                    ->select('item_id', 'service_price', 'service_sale_price')
                    ->get();
            }

            $servicePrices = $keys->pluck('service_price');
            $minServicePrice = $servicePrices->min();
            $maxServicePrice = $servicePrices->max();

            $serviceSalePrice = $keys->pluck('service_sale_price');
            $minServiceSalePrice = $serviceSalePrice->min();

            if ($minServicePrice > $minServiceSalePrice)
            {
                $minPrice = $minServiceSalePrice;
            }
            else
            {
                $minPrice = $minServicePrice;
            }
            $price = array('minPrice' => $minPrice / 100, 'maxPrice' => $maxServicePrice / 100);


            $keys = $keys->groupBy('item_id');

            $itemId = array();
            foreach ($keys as $foo => $key){
                array_push($itemId, $foo);
            }

            $categories = Category::query()
                ->whereIn('id', DB::table('item_category')
                    ->whereIn('item_id', $itemId)
                    ->pluck('category_id'))
                ->withCount($type.' as count')
                ->get();


            $services = Service::query()
                ->whereIn('id', DB::table('item_service')
                    ->whereIn('item_id', $itemId)
                    ->pluck('service_id'))
                ->withCount($type.' as count')
                ->get();

            $platforms = Platform::query()
                ->whereIn('id', DB::table('item_platform')
                    ->whereIn('item_id', $itemId)
                    ->pluck('platform_id'))
                ->withCount($type.' as count')
                ->get();

            $genres = Genre::query()
                ->whereIn('id', DB::table('item_genre')
                    ->whereIn('item_id', $itemId)
                    ->pluck('genre_id'))
                ->withCount($type.' as count')
                ->get();

            return response()->json([
                'price' => $price,
                'categories' => $categories,
                'services' => $services,
                'platforms' => $platforms,
                'genres' => $genres
            ]);
        }
        catch (\Throwable)
        {
            return [
                'type' => 'error',
                'message' => 'Произошла ошибка во время создания фильтров. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400
            ];
        }
    }
}
