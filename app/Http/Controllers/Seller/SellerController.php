<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class SellerController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        try
        {
           $has = Seller::query()->where('user_id', $request->user()->id)->first();
           if ($has !== null)
           {
               return response()->json(['warning' => 'Вы не можете стать продавцом, так как уже им являетесь!',
                   'status' => 400], 400);
           }

            $seller = new Seller;
            $seller->user_id = $request->user()->id;
            $seller->save();
            return response()->json(['message' => 'Вы успешно стали продавцом!',
                'status' => 201], 201);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время становления продавцом. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
