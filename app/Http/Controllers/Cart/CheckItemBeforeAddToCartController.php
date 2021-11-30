<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\InfoAboutItemWhichToCartResource;
use App\Models\Item\GameKey;
use App\Models\Item\SoftwareKey;
use App\Models\Seller\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CheckItemBeforeAddToCartController extends Controller
{
    public function checkItemBeforeAddToCart(Request $request, $type, $keyId): InfoAboutItemWhichToCartResource|JsonResponse
    {
        try
        {
            if ($type === 'game')
            {
                $key = GameKey::query()->with('item', 'seller.user')->where('id', $keyId)->where('bought', 0)->first();
            }
            elseif ($type === 'software')
            {
                $key = SoftwareKey::query()->with('item', 'seller.user')->where('id', $keyId)->where('bought', 0)->first();
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время добавления товара в корзину, выбранный тип товара отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            if ($key === null)
            {
                return response()->json(['warning' => 'К сожалению данного товара уже нет в наличии. Перезагрузите страницу и у вас появится новый самый дешевый вариант данного продукта!',
                    'status' => 400], 400);
            }

            $isSeller = Seller::query()->where('user_id', $request->user()->id)->first()->id;
            if ($key->seller_id === $isSeller)
            {
                return response()->json(['warning' => 'К сожалению, на нашем сервисе запрещено покупать у самого себя товары!',
                    'status' => 400], 400);
            }

            return (new InfoAboutItemWhichToCartResource($key))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время добавления товара в корзину! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
