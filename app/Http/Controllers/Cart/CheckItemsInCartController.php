<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Services\Cart\CheckItemsOnActualityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CheckItemsInCartController extends Controller
{
    private CheckItemsOnActualityService $actualityService;

    public function __construct(CheckItemsOnActualityService $actualityService)
    {
        $this->actualityService = $actualityService;
    }


    public function checkItemsInCart(Request $request): JsonResponse
    {
        try
        {
            return $this->actualityService->checkItems($request->all());
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время запуска сервиса проверки актуальности товаров в корзине! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
