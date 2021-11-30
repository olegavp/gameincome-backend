<?php

namespace App\Http\Controllers\Seller\PublicProfile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Seller\PublicProfile\SellerProfileResource;
use App\Models\Seller\Seller;
use Illuminate\Http\JsonResponse;


class GetSellerProfileController extends Controller
{
    public function get(Seller $seller): JsonResponse|SellerProfileResource
    {
        try
        {
            return SellerProfileResource::make($seller->load('user', 'games', 'software', 'gamePurchases', 'softwarePurchases', 'feedbacks.user', 'reviews'))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время загрузки профиля продавца. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
