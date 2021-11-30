<?php

namespace App\Http\Controllers\User\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Purchase\GamesPurchaseResource;
use App\Http\Resources\User\Purchase\SoftwarePurchaseResource;
use App\Models\Purchase\GamePurchase;
use App\Models\Purchase\SoftwarePurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class GetPurchasesController extends Controller
{
    public function getPurchases(Request $request, $type): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            if ($type === 'games')
            {
                return GamesPurchaseResource::collection(GamePurchase::query()->with('key', 'item', 'item.platforms')->where('user_id', $request->user()->id)->get());
            }
            elseif ($type === 'software')
            {
                return SoftwarePurchaseResource::collection(SoftwarePurchase::query()->with('key', 'item', 'item.platforms')->where('user_id', $request->user()->id)->get());
            }

            return response()->json(['error' => 'Произошла ошибка во время отображения покупок, вы не выбрали тип покупки. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отображения покупок. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
