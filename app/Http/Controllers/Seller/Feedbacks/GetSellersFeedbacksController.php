<?php

namespace App\Http\Controllers\Seller\Feedbacks;

use App\Http\Controllers\Controller;
use App\Http\Resources\SellersFeedbacks\SellersFeedbacksRecommendationsItemsResource;
use App\Http\Resources\SellersFeedbacks\SellersFeedbacksResource;
use App\Models\MainPage\Recommendation;
use App\Models\Seller\SellerFeedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class GetSellersFeedbacksController extends Controller
{
    public function getFeedbacks(): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            return SellersFeedbacksResource::collection(SellerFeedback::query()->with('user', 'gameKey.item', 'softwareKey.item')->orderBy('created_at', 'DESC')->paginate(10))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при загрузке отзывов. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function getItemsUpperFeedbacks(): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            return SellersFeedbacksRecommendationsItemsResource::collection(Recommendation::query()->with('games', 'software')->get())
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при загрузке рекомендаций. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
