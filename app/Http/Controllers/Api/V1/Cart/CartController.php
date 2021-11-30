<?php

namespace App\Http\Controllers\Api\V1\Cart;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Appeals\CreateDisputeAppealController;
use App\Http\Controllers\User\Finances\GetBalanceController;
use App\Http\Requests\Buy\BuyRequest;
use App\Http\Requests\User\PersonalArea\Purchases\CreateDisputeRequest;
use App\Http\Resources\Buy\BuyResource;
use App\Http\Resources\User\Finances\BalanceResource;
use App\Http\Services\Buy\BuyService;
use App\Models\User;
use App\Models\User\PersonalArea\Finance\UserBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Cart",
 *     description="API Endpoints of cart"
 * ),
 */
class CartController extends Controller
{
    private BuyService $buyService;

    public function __construct(BuyService $buyService)
    {
        $this->buyService = $buyService;
    }

    /**
     * @OA\Post (tags={"Cart"}, path="/cart/buy",
     *  security={{"bearer_token":{}}},
     *  operationId="cart-buy",
     *  summary="buy from cart",
     *
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BuyRequest")
     *      ),
     *
     *       @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/BuyResource")
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
    public function buy(BuyRequest $request)
    {
        try
        {
            return $this->buyService->buy($request);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время запуска сервиса покупки товаров! Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
