<?php

namespace App\Http\Controllers\Seller\Sales\AddItem\Key;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\ItemIdRequest;
use App\Http\Resources\Seller\ItemInfoResource;
use App\Models\Item\Game;
use App\Models\Item\Software;
use Illuminate\Http\JsonResponse;


class GetItemInfoAfterSearchController extends Controller
{
    public function getItemInfo(ItemIdRequest $request): JsonResponse|ItemInfoResource
    {
        try
        {
            if ($request->itemType === 'game')
            {
                $gameItem = Game::query()->with('services', 'platforms')->find($request->id);
                return (new ItemInfoResource($gameItem))
                    ->additional(['itemType' => $request->itemType, 'status' => 200]);
            }
            elseif ($request->itemType === 'software')
            {
                $softwareItem = Software::query()->with('services', 'platforms')->find($request->id);
                return (new ItemInfoResource($softwareItem))
                    ->additional(['itemType' => $request->itemType, 'status' => 200]);
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения информации о товаре, выбранная игра или программа отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 404], 404);
            }
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отображения информации о товаре. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
