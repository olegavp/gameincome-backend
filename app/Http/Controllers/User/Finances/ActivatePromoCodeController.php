<?php

namespace App\Http\Controllers\User\Finances;

use App\Http\Controllers\Controller;
use App\Http\Requests\Once\PromoCodeRequest;
use App\Models\AdminPanel\PromoCode\PromoCode;
use App\Models\User\PersonalArea\Finance\Transaction;
use App\Models\User\PersonalArea\Finance\UserBalance;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;


class ActivatePromoCodeController extends Controller
{
    public function activatePromoCode(PromoCodeRequest $request): JsonResponse
    {
        try
        {
            $user = $request->user();

            $promoCode = PromoCode::query()
                ->where('name', $request->name)
                ->where('finish_time', null)
                ->orWhere('finish_time', '>', Carbon::now())
                ->where('count', '!=', 0)
                ->first();
            if ($promoCode === null)
            {
                return response()->json(['warning' => 'Вы ввели неверный промокод!', 'status' => 400], 400);
            }

            $isUsed = Transaction::query()->where(['user_id' => $user->id, 'product' => 'Подарочный код «GameInCome»', 'product_id' => $promoCode->id])->first();
            if ($isUsed !== null)
            {
                return response()->json(['warning' => 'Вы уже использовали данный промокод!', 'status' => 400], 400);
            }


            $userBalance = UserBalance::query()->where('user_id', $user->id)->first();

            DB::transaction(function () use ($userBalance, $promoCode)
            {
                $promoCode->count = $promoCode->count - 1;
                $promoCode->save();

                $userBalance->overall_balance = $promoCode->money + $userBalance->overall_balance;
                $userBalance->available_balance = $promoCode->money + $userBalance->available_balance;
                $userBalance->save();
            });

            $transaction = new Transaction;
            $transaction->user_id = $user->id;
            $transaction->product = 'Подарочный код «GameInCome»';
            $transaction->product_id = $promoCode->id;
            $transaction->operation = 'Подарочный код';
            $transaction->action = 1;
            $transaction->available = 1;
            $transaction->amount = $promoCode->money;
            $transaction->save();

            return response()->json(['message' => 'Код принят! Поздравляем, ваш баланс пополнен на ' . $promoCode->money / 100 . ', приятных покупок.',
                'status' => 200], 200);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время применения промо-кода. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
