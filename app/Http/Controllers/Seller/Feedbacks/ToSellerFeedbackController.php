<?php

namespace App\Http\Controllers\Seller\Feedbacks;

use App\Http\Controllers\Controller;
use App\Http\Resources\SellersFeedbacks\ToSellerFeedbackMoreFeedbacksResource;
use App\Http\Resources\SellersFeedbacks\ToSellerFeedbackResource;
use App\Models\Seller\SellerFeedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ToSellerFeedbackController extends Controller
{
    public function getFeedback(SellerFeedback $sellerFeedback): JsonResponse|ToSellerFeedbackResource
    {
        try
        {
            return (new ToSellerFeedbackResource($sellerFeedback->load('user', 'gameKey.item', 'softwareKey.item')))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при загрузке отзыва. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function getMoreFeedbacksInFeedback(SellerFeedback $sellerFeedback): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            return ToSellerFeedbackMoreFeedbacksResource::collection(SellerFeedback::query()->with('user', 'gameKey.item', 'softwareKey.item')->where('user_id', $sellerFeedback->user_id)->where('id', '!=', $sellerFeedback->id)->orderBy('created_at', 'DESC')->paginate(12))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при загрузке других отзывов от этого пользователя. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
