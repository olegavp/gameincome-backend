<?php

namespace App\Http\Controllers\Seller\Sales\ItemsOnSale\Software;

use App\Http\Controllers\Controller;
use App\Models\Item\SoftwareKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ToArchiveSoftwareController extends Controller
{
    public function toArchiveSoftware(Request $request, $id): JsonResponse
    {
        try
        {
            $sellerId = $request->user()->seller->id;
            $id =  SoftwareKey::query()->where(['item_id' => $id, 'seller_id' => $sellerId, 'bought' => 0])->withoutTrashed()->select('id')->get();
            if ($id->isEmpty())
            {
                return response()->json(['warning' => 'Софта, который вы добавляете в архив нет в вашем списке активных продаж.',
                    'status' => 400], 400);
            }

            SoftwareKey::query()->where(['seller_id' => $sellerId, 'bought' => 0])->whereIn('id', $id)->delete();

            return response()->json(['message' => 'Вы добавили ключи данного софта в архив.',
                'status' => 201], 201);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время добавлении ваших софтовых ключей в архив. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
