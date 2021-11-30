<?php

namespace App\Http\Controllers\Api\V1\PersonalArea\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Once\PromoCodeRequest;
use App\Http\Requests\Seller\Sales\SalesRequest;
use App\Http\Resources\User\Finances\BalanceResource;
use App\Http\Resources\User\Finances\TransactionsResource;
use App\Http\Resources\User\Sales\SaleResource;
use App\Models\AdminPanel\PromoCode\PromoCode;
use App\Models\User;
use App\Models\User\PersonalArea\Appeals\DisputeAppeal;
use App\Models\User\PersonalArea\Finance\Transaction;
use App\Models\User\PersonalArea\Finance\UserBalance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use OpenApi\Annotations as OA;


class FinanceController extends Controller
{
    private int $freezeLivePeriod = (14 * 24 * 60 * 60); // sec

    /**
     * @OA\Get (tags={"Personal Area: Finance"}, path="/personal-area/finance/balance",
     *  security={{"bearer_token":{}}},
     *  operationId="personal_area_finance_balance",
     *  summary="Personal Area Finance",
     *
     *       @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/BalanceResource")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request - Произошла ошибка: Способ оплаты временно недоступен, попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden - Нет прав для выполнения запроса"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
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

            $newUser = User::where('id', $user->id)->with('balance', 'seller')->first();
            $newUser->balance->blocking_balance = 0;
            //TODO проверяем наличие открытых диспутов
            $DisputeAppeal = DisputeAppeal::where('seller_id', $newUser->seller->id);
            if ($DisputeAppeal->exists()){
                $disputes = $DisputeAppeal->get();
                foreach ($disputes as $dispute):
                    $Transaction = Transaction::where([
                        ['user_id', $newUser->id], // транзакции пользователя
                        ['item_type', $dispute->item_type], // тип товара
                        ['key_id', $dispute->key_id], // id товара(ключа)
                        ['action', 1], // статус продавец
                    ])->first();
                    $newUser->balance->blocking_balance += $Transaction->amount;
                endforeach;
            }
            return (new BalanceResource($newUser))
                ->additional(['status' => 200]);
        } catch (\Throwable) {
            return response()->json(['error' => 'Произошла ошибка во время загрузки баланса. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

    /**
     * @OA\Get (tags={"Personal Area: Finance"}, path="/personal-area/finance/transactions",
     *  security={{"bearer_token":{}}},
     *  operationId="personal_area_finance_transactions",
     *  summary="Personal Area Finance",
     *
     *       @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/TransactionsResource")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden / Недостаточно прав для выполнения запроса"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function getTransactions(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            return TransactionsResource::collection(Transaction::query()->where('user_id', $request->user()->id)->orderByDesc('created_at')->paginate('15'))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время загрузки транзакций. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

    /**
     * @OA\Post (tags={"Personal Area: Finance"}, path="/personal-area/finance/promo-code/{name}/activate",
     *  security={{"bearer_token":{}}},
     *  operationId="personal_area_finance_promo_code_activate",
     *  summary="Personal Area Finance",
     *
     *      @OA\Parameter(name="name",
     *          required=true,
     *          in="path",
     *      ),
     *
     *       @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden / Недостаточно прав для выполнения запроса"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
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
