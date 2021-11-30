<?php

namespace App\Http\Controllers\Seller\Feedbacks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\SellerFeedbackRequest;
use App\Http\Services\SellersFeedback\CreateFeedbackOnSeller;
use Illuminate\Http\JsonResponse;


class CreateFeedbackOnSellerController extends Controller
{
    private CreateFeedbackOnSeller $createFeedbackOnSeller;

    public function __construct(CreateFeedbackOnSeller $createFeedbackOnSeller)
    {
        $this->createFeedbackOnSeller = $createFeedbackOnSeller;
    }

    public function createFeedback(SellerFeedbackRequest $request): JsonResponse
    {
        try
        {
            /*
             * @description Передаём в параметры $request для создания отзыва о ключе
             * @param Request
             * @return Json
             * @throw Json
             */
            return $this->createFeedbackOnSeller->create($request);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время работы сервиса создания отзыва на продавца. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
