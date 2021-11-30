<?php

namespace App\Http\Controllers\Items;

use App\Http\Controllers\Controller;
use App\Http\Resources\Items\CatalogItemsResource;
use App\Models\Item\Game;
use App\Models\Item\GameKey;
use App\Models\Item\Software;
use App\Models\Item\SoftwareKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class GetItemsController extends Controller
{
    public function get(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $type = $request['type'];
        $sort = ($request['sort']) ? $request['sort'] : 'desc';
        try
        {
            if ($type === 'games')
            {
                return CatalogItemsResource::collection(GameKey::query()
                    ->with('item')
                    ->where('bought', 0)
                    ->select('item_id', 'seller_price', 'service_price', 'seller_sale_price', 'service_sale_price')
                    ->groupBy('item_id')
                    ->orderBy(Game::select('metacritic')
                        ->whereColumn('games.id', 'item_id'), $sort
                    )
                    ->paginate(15)
                )->additional(['status' => 200]);
            }
            elseif ($type === 'software')
            {
                return CatalogItemsResource::collection(SoftwareKey::query()
                    ->with('item')
                    ->where('bought', 0)
                    ->select('item_id', 'seller_price', 'service_price', 'seller_sale_price', 'service_sale_price')
                    ->groupBy('item_id')
                    ->orderBy(Software::select('metacritic')
                        ->whereColumn('software.id', 'item_id'), $sort
                    )
                    ->paginate(15)
                )->additional(['status' => 200]);
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения товаров, выбранный тип товаров отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время загрузки каталога. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
