<?php

namespace App\Http\Services\Buy;

use App\Http\Resources\Buy\BuyResource;
use App\Http\Services\Cart\CheckItemsOnActualityService;
use Illuminate\Support\Facades\Log;


class BuyService extends CheckItemsOnActualityService
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }


    public function buy($request)
    {
        try
        {
            if ($request->paymentMethod !== 'card' and $request->paymentMethod !== 'balance')
            {
                return response()->json(['error' => 'Произошла ошибка во время покупки товара, выбранный тип оплаты отсутствует на нашем сервисе! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }
            /*
             * @description проверяем наличие товара. Если товаров некоторых нет, то оплата тех, которые есть. Если возвращает
             * пустоту, то выводим ошибку о том, что данных товаров уже нет в наличии.
             * @param Request
             * @return Json data['frontEnd', 'dataBase', 'different']
             * @throwable Json
             */
//            $chekOnActuality = $this->checkItems(json_decode($request->items, true));
            $chekOnActuality = $this->checkItems($request->items);
            if ($chekOnActuality->status() === 400)
            {
                return $chekOnActuality;
            }
            if ($chekOnActuality->original['dataBase']->isEmpty())
            {
                return response()->json(['warning' => 'Произошла ошибка во время списывании товаров, все товары уже куплены! Попробуйте перезагрузить страницу, должны появиться предложения от дургих продавцов, Спасибо!',
                    'status' => 400], 400);
            }

            /*
             * @description передаём в сервис оплаты
             * @param Collection $chekOnActuality ['frontEnd', 'dataBase', 'different']
             * @return
             * @throwable Json
             */
            if ($request->paymentMethod === 'card')
            {
                $paymentResponse = $this->paymentService->cardPayment($chekOnActuality, $request);
                if (get_class($paymentResponse) === 'Illuminate\Http\JsonResponse')
                {
                    return $paymentResponse;
                }
            }
            elseif ($request->paymentMethod === 'balance')
            {
                $paymentResponse = $this->paymentService->balancePayment($chekOnActuality, $request);
                if (get_class($paymentResponse) === 'Illuminate\Http\JsonResponse')
                {
                    return $paymentResponse;
                }
            }
            else
            {
                return response()->json(['warning' => 'Недостаточно средств на карте',
                    'status' => 400], 400);
//                $paymentResponse = $this->paymentService->balancePayment($chekOnActuality, $request);
//                if (get_class($paymentResponse) === 'Illuminate\Http\JsonResponse')
//                {
//                    return $paymentResponse;
//                }
            }

            return BuyResource::collection($paymentResponse)
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время работы сервиса покупки! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
