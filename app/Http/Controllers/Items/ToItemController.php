<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Resources\Items\ToItemMoreSellersResource;
use App\Http\Resources\Items\ToItemResource;
use App\Models\Item\Game;
use App\Models\Item\GameKey;
use App\Models\Item\Software;
use App\Models\Item\SoftwareKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ToItemController extends Controller
{
    public function info($type, $itemId)
    {
        try
        {
            if ($type === 'game')
            {
                $itemModel = Game::query();
            }
            elseif ($type === 'software')
            {
                $itemModel = Software::query();
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения информации о товаре, выбранный тип товара отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $item = $itemModel->with('genres', 'categories', 'platforms', 'services')->find($itemId);
            if ($item === null)
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения информации о товаре, товар отсутствует на нашем сервисе!',
                    'status' => 404], 404);
            }


            if ($type === 'game')
            {
                $keyWithoutSale = GameKey::query()
                    ->with('seller', 'seller.user', 'seller.feedbacks', 'seller.feedbacks.user')
                    ->where('item_id', $item->id)
                    ->where('bought', 0)
                    ->where('service_price', '!=', null)
                    ->orderBy('service_price', 'ASC')
                    ->first();

                $keyWithSale = GameKey::query()
                    ->with('seller', 'seller.user', 'seller.feedbacks', 'seller.feedbacks.user')
                    ->where('item_id', $item->id)
                    ->where('bought', 0)
                    ->where('service_sale_price', '!=', null)
                    ->orderBy('service_sale_price', 'ASC')
                    ->first();
            }
            if ($type === 'software')
            {
                $keyWithoutSale = SoftwareKey::query()
                    ->with('seller', 'seller.user', 'seller.feedbacks', 'seller.feedbacks.user')
                    ->where('item_id', $item->id)
                    ->where('bought', 0)
                    ->where('service_price', '!=', null)
                    ->orderBy('service_price', 'ASC')
                    ->first();

                $keyWithSale = SoftwareKey::query()
                    ->with('seller', 'seller.user', 'seller.feedbacks', 'seller.feedbacks.user')
                    ->where('item_id', $item->id)
                    ->where('bought', 0)
                    ->where('service_sale_price', '!=', null)
                    ->orderBy('service_sale_price', 'ASC')
                    ->first();
            }
            if ($keyWithoutSale === null and $keyWithSale === null)
            {
                return (new ToItemResource($item))
                    ->additional(['status' => 200]);
            }

            if($keyWithSale !== null)
            {
                if ($keyWithoutSale->service_price > $keyWithSale->service_sale_price)
                {
                    $key = $keyWithSale;
                }
                else
                {
                    $key = $keyWithoutSale;
                }
            }
            else
            {
                $key = $keyWithoutSale;
            }

            $item = collect($item)->merge($key);
            $item = json_decode($item);

            return (new ToItemResource($item))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время перехода на товар. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function moreSellers($type, $itemId, $sellerId): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            if ($type === 'game')
            {
                $keyModel = GameKey::query();
            }
            elseif ($type === 'software')
            {
                $keyModel = SoftwareKey::query();
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения продавцов по данному продукту, выбранный тип товара отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $key = $keyModel->with('seller', 'seller.user', 'seller.feedbacks')->where('item_id', $itemId)->where('seller_id', '!=', $sellerId)->where('bought', 0)->orderBy('service_price', 'ASC')->paginate(15);

            return ToItemMoreSellersResource::collection($key)
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при загрузке всех продавцов по данному товару. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
