<?php

namespace App\Http\Controllers\Api\V1\PersonalArea\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Purchase\KeyPurchaseResource;
use App\Http\Resources\User\Purchase\PurchaseResource;
use App\Models\Purchase\GamePurchase;
use App\Models\Purchase\SoftwarePurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;


class PurchasesController extends Controller
{
    /**
     * @OA\Get (tags={"Personal Area: Purchases"}, path="/personal-area/purchases/{itemType}",
     *   security={{"bearer_token":{}}},
     *   operationId="purchases",
     *   summary="get purchases",
     *
     *      @OA\Parameter(name="itemType", required=true, in="path", example="games", description="games / software / swiches / cases",),
     *
     *       @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/PurchaseResource")
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
     *          description="Forbidden - Недостаточно прав для выполнения запроса"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function purchases(Request $request, $itemType): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            if ($itemType === 'games')
            {
                return PurchaseResource::collection(GamePurchase::query()->with('key', 'item', 'item.platforms')->where('user_id', $request->user()->id)->get());
            }
            elseif ($itemType === 'software')
            {
                return PurchaseResource::collection(SoftwarePurchase::query()->with('key', 'item', 'item.platforms')->where('user_id', $request->user()->id)->get());
            }

            return response()->json(['error' => 'Произошла ошибка во время отображения покупок, вы не выбрали тип покупки. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отображения покупок. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

    /**
     * @OA\Get (tags={"Personal Area: Purchases"}, path="/personal-area/purchases/{itemType}/{keyId}",
     *   security={{"bearer_token":{}}},
     *   operationId="purchase",
     *   summary="get purchase",
     *
     *      @OA\Parameter(name="itemType", required=true, in="path", example="games", description="games / software / swiches / cases",),
     *      @OA\Parameter(name="keyId", required=true, in="path",),
     *
     *       @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/KeyPurchaseResource")
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
     *          description="Forbidden - Недостаточно прав для выполнения запроса"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function purchase(Request $request, $itemType, $keyId): JsonResponse
    {
        try
        {
            if ($itemType === 'games')
            {
                $user = $request->user()->id;
                $purchase = GamePurchase::query()->with('key', 'item', 'seller', 'seller.user')->where(['key_id' => $keyId, 'user_id' => $user])->first();
                if ($purchase === null)
                {
                    return response()->json(['warning' => 'Мы не нашли информации по данной покупке!',
                        'status' => 400], 400);
                }

                return response()->json([new KeyPurchaseResource($purchase), 'status' => 200]);
            }
            elseif ($itemType === 'software')
            {
                $user = $request->user()->id;
                $purchase = SoftwarePurchase::query()->with('key', 'item', 'seller', 'seller.user')->where(['key_id' => $keyId, 'user_id' => $user])->first();
                if ($purchase === null)
                {
                    return response()->json(['warning' => 'Мы не нашли информации по данной покупке!',
                        'status' => 400], 400);
                }

                return response()->json([new KeyPurchaseResource($purchase), 'status' => 200]);
            }

            return response()->json(['error' => 'Произошла ошибка во время отображения покупки, вы не выбрали тип покупки. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отображения информации. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

}
