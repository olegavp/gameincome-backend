<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Requests\Items\FilterRequest;
use App\Http\Resources\Items\CatalogItemsResource;
use App\Models\Item\GameKey;
use App\Models\Item\SoftwareKey;
use Illuminate\Support\Facades\DB;


class GetItemsAfterFilterController extends Controller
{
    public function get(FilterRequest $request)
    {
        try
        {
            if ($request->itemType !== 'games' and $request->itemType !== 'software')
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения товаров после работы фильтра, выбранный тип товара отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            if ($request->itemType === 'games')
            {
                $priceItemId = GameKey::query()->where('bought', 0)
                    ->whereBetween('service_price', [$request->minPrice * 100, $request->maxPrice * 100])
                    ->select('id')
                    ->get();

                $salePriceItemId = GameKey::query()->where('bought', 0)
                    ->whereBetween('service_sale_price', [$request->minPrice * 100, $request->maxPrice * 100])->where('service_sale_price', '!=', null)
                    ->select('id')
                    ->get();

                $priceId = $priceItemId->merge($salePriceItemId)->unique('id')->pluck('id');
            }
            else
            {
                $priceItemId = SoftwareKey::query()->where('bought', 0)
                    ->whereBetween('service_price', [$request->minPrice * 100, $request->maxPrice * 100])
                    ->select('id')
                    ->get();

                $salePriceItemId = SoftwareKey::query()->where('bought', 0)
                    ->whereBetween('service_sale_price', [$request->minPrice * 100, $request->maxPrice * 100])->where('service_sale_price', '!=', null)
                    ->select('id')
                    ->get();

                $priceId = $priceItemId->merge($salePriceItemId)->unique('id')->pluck('id');
            }


            $categoriesItemsId = collect();
            $servicesItemsId = collect();
            $platformsItemsId = collect();
            $genresItemsId = collect();
            if(isset($request->categories))
            {
                $categories = explode(',', $request->categories);
                $categoriesItemsId = DB::table('item_category')
                    ->whereIn('category_id', $categories)
                    ->select('item_id')
                    ->distinct('item_id')
                    ->pluck('item_id');
            }

            if (isset($request->platforms))
            {
                $platforms = explode(',', $request->platforms);
                $platformsItemsId = DB::table('item_platform')
                    ->whereIn('platform_id', $platforms)
                    ->distinct('item_id')
                    ->pluck('item_id');
            }

            if (isset($request->services))
            {
                $services = explode(',', $request->services);
                $servicesItemsId = DB::table('item_service')
                    ->whereIn('service_id', $services)
                    ->distinct('item_id')
                    ->pluck('item_id');
            }


            if (isset($request->genres))
            {
                $genres = explode(',', $request->genres);
                $genresItemsId = DB::table('item_genre')
                    ->whereIn('genre_id', $genres)
                    ->distinct('item_id')
                    ->pluck('item_id');
            }

            $processedItemId = $categoriesItemsId->merge($servicesItemsId)->merge($platformsItemsId)->merge($genresItemsId);
            $processedItemId = $processedItemId->unique();


            if ($request->itemType === 'games')
            {
                $afterPriceId = GameKey::query()->where('bought', 0)->whereIn('id', $priceId)->get();
                if ($processedItemId->isNotEmpty())
                {
                    $items = $afterPriceId->whereIn('item_id', $processedItemId)->pluck('id');
                }
                else
                {
                    $items = $afterPriceId->pluck('id');
                }

                return CatalogItemsResource::collection(GameKey::query()
                    ->with('item')
                    ->whereIn('id', $items)
                    ->groupBy('item_id')
                    ->paginate(15))
                    ->additional(['status' => 200]);
            }
            else
            {
                $afterPriceId = SoftwareKey::query()->where('bought', 0)->whereIn('id', $priceId)->get();
                if ($processedItemId->isNotEmpty())
                {
                    $items = $afterPriceId->whereIn('item_id', $processedItemId)->pluck('id');
                }
                else
                {
                    $items = $afterPriceId->pluck('id');
                }

                return CatalogItemsResource::collection(GameKey::query()
                    ->with('item')
                    ->whereIn('id', $items)
                    ->groupBy('item_id')
                    ->paginate(15))
                    ->additional(['status' => 200]);
            }
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время загрузки каталога после фильтров. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
