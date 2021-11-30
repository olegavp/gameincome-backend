<?php

namespace App\Http\Controllers\User\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class GetProfileInfoController extends Controller
{
    public function getProfileInfo(Request $request): JsonResponse|UserResource
    {
        try {
            $user = $request->user()->load('gamePurchases', 'softwarePurchases', 'feedbacks');

            return (new UserResource($user))->additional([
                'purchaseCount' => $user->gamePurchases->count() + $user->softwarePurchases->count(),
                'feedbackOnSellersCount' => $user->feedbacks->count(),
                'reviewsCount' => 0,
                'status' => 200
            ]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отображения страницы профиля. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
