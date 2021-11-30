<?php

namespace App\Http\Controllers\User\Finances;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Finances\BalanceResource;
use App\Models\User;
use App\Models\User\PersonalArea\Finance\Transaction;
use App\Models\User\PersonalArea\Finance\UserBalance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;


class GetBalanceController extends Controller
{
    private int $freezeLivePeriod = (14 * 24 * 60 * 60); // sec

    public function getBalance(Request $request): JsonResponse|BalanceResource
    {
        try {
            $user = $request->user()->load('balance');
            $balance = $user->balance;
            // проврека заморозок
//            Log::info(date('Y-m-d H:i:s', (time() - $this->freezeLivePeriod)));
            if ($balance->pending_balance) {
                // поиск непроведенных транзакций
                $Transaction = Transaction::where([
                    ['available', 0], // доступность средств для ввода
                    ['action', 1], // признак продавца
                    ['user_id', $user->id], // хозяин транзакции
                    ['created_at', '<', date('Y-m-d H:i:s', (time() - $this->freezeLivePeriod))] // период заморозки
                ]);
                if ($Transaction->exists()) {
                    // обновление баланса пользователя
//                    Log::info($Transaction->get());
                    foreach ($Transaction->get() as $transaction):
                        if ($balance->pending_balance >= $transaction->amount){ // замороженный баланс не должен уходить в минус
                            // размораживаем баланс на размер транзакции
                            $UserBalance = UserBalance::where('user_id', $transaction->user_id)
                                ->update([
                                    'pending_balance' => $balance->pending_balance - $transaction->amount,
                                    'available_balance' => $balance->available_balance + $transaction->amount
                                ]);
                            if ($UserBalance > 0) { // только если были изменения балансов
                                // размораживаем транзакцию
                                $transaction->available = 1;
                                $transaction->save();
                            }
                        }
                    endforeach;
                }
            }

            //TODO проверка блокирвок
            if ($balance->blocked_balance) {
                //TODO поиск незакрытых диспутов
                Log::info('blocked_balance');
            }


                $newUser = User::where('id', $user->id)->with('balance')->first();
            return (new BalanceResource($newUser))
                ->additional(['status' => 200]);
        } catch (\Throwable) {
            return response()->json(['error' => 'Произошла ошибка во время загрузки баланса. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
