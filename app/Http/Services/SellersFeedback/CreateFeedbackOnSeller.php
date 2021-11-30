<?php

namespace App\Http\Services\SellersFeedback;

use App\Models\Purchase\SoftwarePurchase;
use App\Models\Seller\SellerFeedback;
use App\Models\Purchase\GamePurchase;
use Illuminate\Http\JsonResponse;


class CreateFeedbackOnSeller
{
    public function create($data): JsonResponse
    {
        try
        {
            if ($data->itemType === 'game')
            {
                $purchase = GamePurchase::query()->where(['user_id' => $data->user()->id, 'seller_id' => $data->sellerId, 'key_id' => $data->keyId])->first();
                if ($purchase === null)
                {
                    return response()->json(['warning' => 'Вы не можете оставить отзыв по данному ключу!',
                        'status' => 400], 400);
                }
            }
            elseif ($data->itemType === 'software')
            {
                $purchase = SoftwarePurchase::query()->where(['user_id' => $data->user()->id, 'seller_id' => $data->sellerId, 'key_id' => $data->keyId])->first();
                if ($purchase === null)
                {
                    return response()->json(['warning' => 'Вы не можете оставить отзыв по данному ключу!',
                        'status' => 400], 400);
                }
            }
            else
            {
                return response()->json(['error' => 'Данный тип предмета не прошёл валидацию!',
                    'status' => 400], 400);
            }

            $sellerFeedback = SellerFeedback::query()->where(['user_id' => $data->user()->id, 'key_id' => $data->keyId])->first();
            if ($sellerFeedback !== null)
            {
                return response()->json(['warning' => 'Вы уже оставили отзыв по данной покупке!',
                    'status' => 400], 400);
            }

            $sellerFeedback = new SellerFeedback;
            $sellerFeedback->seller_id = $data->sellerId;
            $sellerFeedback->user_id = $data->user()->id;
            $sellerFeedback->key_id = $data->keyId;
            $sellerFeedback->rate = $data->rate;
            $sellerFeedback->comment = $data->comment;
            $sellerFeedback->item_type = $data->itemType;
            $sellerFeedback->save();

            return response()->json(['data' => 'Вы успешно добавили отзыв об этом продавце!',
                'status' => 201], 201);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время работы сервиса создания отзыва на продавца. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
