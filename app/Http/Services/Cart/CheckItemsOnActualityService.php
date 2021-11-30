<?php

namespace App\Http\Services\Cart;

use App\Models\Item\GameKey;
use App\Models\Item\SoftwareKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


class CheckItemsOnActualityService
{
    public function checkItems($request): JsonResponse
    {
        try
        {
            $gameKeysFromFrontEnd = collect();
            $softwareKeysFromFrontEnd = collect();
            foreach ($request as $key => $item)
            {
                if ($item['itemType'] === 'game')
                {
                    $gameKeysFromFrontEnd[$key] = ['keyId' => $item['keyId'], 'itemType' => $item['itemType']];
                }
                if ($item['itemType'] === 'software')
                {
                    $softwareKeysFromFrontEnd[$key] = ['keyId' => $item['keyId'], 'itemType' => $item['itemType']];
                }
            }


            $gameKeysFromDb = null;
            $softwareKeysFromDb = null;
            if (count($gameKeysFromFrontEnd) > 0)
            {
                $gameKeysFromDb = GameKey::query()->whereIn('id', $gameKeysFromFrontEnd->pluck('keyId'))->where('bought', 0)->pluck('id');
            }
            if (count($softwareKeysFromFrontEnd) > 0)
            {
                $softwareKeysFromDb = SoftwareKey::query()->whereIn('id', $softwareKeysFromFrontEnd->pluck('keyId'))->where('bought', 0)->pluck('id');
            }

            $keysFromFrontEnd = $gameKeysFromFrontEnd->merge($softwareKeysFromFrontEnd);
            $keysFromDb = $gameKeysFromDb->merge($softwareKeysFromDb);
            $different = $keysFromFrontEnd->pluck('keyId')->diff($keysFromDb)->values();
            foreach ($different as $diffKey => $item)
            {
                foreach ($keysFromFrontEnd as $keyId)
                {
                    if ($item === $keyId['keyId'])
                    {
                        $different[$diffKey] = ['keyId' => $keyId['keyId'], 'itemType' => $keyId['itemType']];
                    }
                }
            }

            return response()->json(['frontEnd' => $keysFromFrontEnd, 'dataBase' => $keysFromDb, 'different' => $different, 'status' => 200], 200);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время проверки актуальности товаров в корзине! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
