<?php

namespace App\Http\Controllers\Seller\Sales\ItemsOnSale\Games;

use App\Http\Controllers\Controller;
use App\Models\Item\GameKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ToArchiveGamesController extends Controller
{
    public function toArchiveGames(Request $request, $id): JsonResponse
    {
        try
        {
            $sellerId = $request->user()->seller->id;
            $id =  GameKey::query()->where(['item_id' => $id, 'seller_id' => $sellerId, 'bought' => 0])->withoutTrashed()->select('id')->get();
            if ($id->isEmpty())
            {
                return response()->json(['warning' => 'Игры, которую вы добавляете в архив нет в вашем списке активных продаж.',
                    'status' => 400], 400);
            }

            GameKey::query()->where(['seller_id' => $sellerId, 'bought' => 0])->whereIn('id', $id)->delete();

            return response()->json(['message' => 'Вы добавили ключи данной игры в архив.',
                'status' => 201], 201);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время добавлении ваших игровых ключей в архив. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
