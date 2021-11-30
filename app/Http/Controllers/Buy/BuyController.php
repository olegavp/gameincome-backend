<?php

namespace App\Http\Controllers\Buy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buy\BuyRequest;
use App\Http\Services\Buy\BuyService;
use OpenApi\Annotations as OA;


class BuyController extends Controller
{
    private BuyService $buyService;

    public function __construct(BuyService $buyService)
    {
        $this->buyService = $buyService;
    }



    public function buy(BuyRequest $request)
    {
        try
        {
            return $this->buyService->buy($request);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время запуска сервиса покупки товаров! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
