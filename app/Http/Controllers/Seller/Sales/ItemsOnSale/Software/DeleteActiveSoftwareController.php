<?php

namespace App\Http\Controllers\Seller\Sales\ItemsOnSale\Software;

use App\Http\Controllers\Controller;
use App\Models\Item\SoftwareKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class DeleteActiveSoftwareController extends Controller
{
    public function deleteActiveSoftware(Request $request, $id): JsonResponse
    {
        try
        {
            $sellerId = $request->user()->seller->id;
            $id =  SoftwareKey::query()->where(['item_id' => $id, 'seller_id' => $sellerId, 'bought' => 0])->select('id')->get();
            if ($id->isEmpty())
            {
                return response()->json(['warning' => 'Софта, который вы удаляете из нашего сервиса нет в вашем списке.',
                    'status' => 400], 400);
            }

            SoftwareKey::query()->where('bought', 0)->whereIn('id', $id)->forceDelete();

            return response()->json(['message' => 'Вы удалили ключи данного софта с нашего сервиса.',
                'status' => 201], 201);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время удаления софтовых ключей из нашего сервиса. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
