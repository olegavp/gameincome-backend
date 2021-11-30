<?php

namespace App\Http\Services\Buy;

use App\Models\Item\GameKey;
use App\Models\Item\SoftwareKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class WriteOffService
{
    public function defineKeys($items): JsonResponse|array
    {
        try
        {
            $items = $items->original;
            if ($items['frontEnd']->isEmpty())
            {
                return response()->json(['error' => 'Произошла ошибка во время считывания товаров, ключи не были переданы! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $intersectItems = $items['frontEnd']->pluck('keyId')->intersect($items['dataBase']);
            foreach ($intersectItems as $intersectKey => $item)
            {
                foreach ($items['frontEnd'] as $keyId)
                {
                    if ($item === $keyId['keyId'])
                    {
                        $intersectItems[$intersectKey] = ['keyId' => $keyId['keyId'], 'itemType' => $keyId['itemType']];
                    }
                }
            }

            $gameKeys = $intersectItems->where('itemType', 'game')->pluck('keyId');
            $softwareKeys = $intersectItems->where('itemType', 'software')->pluck('keyId');

            return ['gameKeys' => $gameKeys, 'softwareKeys' => $softwareKeys];
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время считывания товаров! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function writeOff($gameKeys, $softwareKeys)
    {
        try
        {
            DB::transaction(function () use ($gameKeys, $softwareKeys)
            {
                if ($gameKeys->isNotEmpty())
                {
                    GameKey::query()->whereIn('id', $gameKeys->pluck('id'))->update(['bought' => 1]);
                }

                if ($softwareKeys->isNotEmpty())
                {
                    SoftwareKey::query()->whereIn('id', $softwareKeys->pluck('id'))->update(['bought' => 1]);
                }
            });

            return $gameKeys->merge($softwareKeys);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время списывания товаров! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
