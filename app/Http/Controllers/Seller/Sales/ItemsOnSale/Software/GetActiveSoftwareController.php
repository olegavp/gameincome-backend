<?php

namespace App\Http\Controllers\Seller\Sales\ItemsOnSale\Software;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Sales\Software\SaleSoftwareResource;
use App\Models\Item\SoftwareKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class GetActiveSoftwareController extends Controller
{
    public function getActiveSoftware(Request $request): JsonResponse
    {
        try
        {
            $collection = SaleSoftwareResource::collection(SoftwareKey::query()->with('item', 'item.services', 'item.platforms')->where(['seller_id' => $request->user()->seller->id, 'bought' => 0])->get());
            $collection = collect($collection)->groupBy(['itemId', 'oldPrice']);

            return response()->json(['data' => $collection, 'status' => 200], 200);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отображения активных софтовых ключей на продаже. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
