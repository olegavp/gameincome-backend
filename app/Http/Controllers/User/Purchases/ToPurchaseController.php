<?php

namespace App\Http\Controllers\User\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Purchase\GameKeyPurchaseResource;
use App\Http\Resources\User\Purchase\SoftwareKeyPurchaseResource;
use App\Models\Purchase\GamePurchase;
use App\Models\Purchase\SoftwarePurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ToPurchaseController extends Controller
{
    public function toPurchase($type, $key, Request $request): GameKeyPurchaseResource|SoftwareKeyPurchaseResource|JsonResponse
    {
        try
        {
            if ($type === 'game')
            {
                $user = $request->user()->id;
                $purchase = GamePurchase::query()->with('key', 'item', 'seller', 'seller.user')->where(['key_id' => $key, 'user_id' => $user])->first();
                if ($purchase === null)
                {
                    return response()->json(['warning' => 'Мы не нашли информации по данной покупке!',
                        'status' => 400], 400);
                }

                return new GameKeyPurchaseResource($purchase);
            }
            elseif ($type === 'software')
            {
                $user = $request->user()->id;
                $purchase = SoftwarePurchase::query()->with('key', 'item', 'seller', 'seller.user')->where(['key_id' => $key, 'user_id' => $user])->first();
                if ($purchase === null)
                {
                    return response()->json(['warning' => 'Мы не нашли информации по данной покупке!',
                        'status' => 400], 400);
                }

                return new SoftwareKeyPurchaseResource($purchase);
            }

            return response()->json(['error' => 'Произошла ошибка во время отображения покупки, вы не выбрали тип покупки. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отображения информации. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
