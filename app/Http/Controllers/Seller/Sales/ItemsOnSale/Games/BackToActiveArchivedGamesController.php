<?php

namespace App\Http\Controllers\Seller\Sales\ItemsOnSale\Games;

use App\Http\Controllers\Controller;
use App\Models\Item\GameKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BackToActiveArchivedGamesController extends Controller
{
    public function backToActiveArchivedGames(Request $request, $id): JsonResponse
    {
        try
        {
            $sellerId = $request->user()->seller->id;
            $id =  GameKey::query()->where(['item_id' => $id, 'seller_id' => $sellerId, 'bought' => 0])->select('id')->onlyTrashed()->get();
            if ($id->isEmpty())
            {
                return response()->json(['warning' => 'Игры, которую вы убираете из архива нет в вашем списке.',
                    'status' => 400], 400);
            }

            GameKey::query()->where(['seller_id' => $sellerId, 'bought' => 0])->whereIn('id', $id)->restore();

            return response()->json(['message' => 'Вы убрали ключи данной игры из архива и выставили вновь на активные продажи!',
                'status' => 201], 201);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время перемещения из архива игровых ключей в раздел активных продаж. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
