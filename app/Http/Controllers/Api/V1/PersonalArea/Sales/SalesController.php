<?php

namespace App\Http\Controllers\Api\V1\PersonalArea\Sales;

use App\Http\Controllers\Api\V1\Types\TypesController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\Sales\SalesRequest;
use App\Http\Requests\Seller\Sales\SalesUpdateRequest;
use App\Http\Resources\User\Sales\SaleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;


class SalesController extends Controller
{

    /**
     * @OA\Get (tags={"Personal Area: Sales"}, path="/personal-area/sales/{itemType}",
     *   security={{"bearer_token":{}}},
     *   operationId="sales",
     *   summary="get items",
     *
     *      @OA\Parameter(name="itemType", required=true, in="path", example="games", description="games / software / swiches / cases",),
     *      @OA\Parameter(name="state", required=false, in="query", description="bought / archived",),
     *
     *       @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/SaleResource")
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
    public function sales(SalesRequest $request, $itemType): JsonResponse
    {
        // определяем тип запроса
        $itemTypes = TypesController::itemTypes();
        if (isset($itemTypes[$itemType])) {
            $itemType = $itemTypes[$itemType];
        } else {
            return response()->json(['error' => 'Тип ' . $itemType . ' отсутствует в апи!', 'status' => 400], 400);
        }
        // обрабатываем запрос
        try {
            // статус продажи
            $bought = 0;
            if (isset($request['state']) && $request['state'] == 'bought') {
                $bought = 1;
            }
            // собираем модель в рамках запроса
            $model = $itemType['modelKey']::with('item', 'item.services', 'item.platforms')->where(['seller_id' => $request->user()->seller->id, 'bought' => $bought]);
            if (isset($request['state']) && $request['state'] == 'archived') { // архивные
                $model = $model->onlyTrashed();
            }
            // генерируем данные для получателя
            $collection = SaleResource::collection($model->get());
            $collection = collect($collection)->groupBy(['itemId', 'oldPrice']);

            return response()->json(['data' => $collection, 'status' => 200], 200);
        } catch (\Throwable) {
            return response()->json(['error' => $itemType['error'], 'status' => 400], 400);
        }
    }

    /**
     * @OA\Post (tags={"Personal Area: Sales"}, path="/personal-area/sales/{itemType}",
     *   security={{"bearer_token":{}}},
     *  operationId="sales_create",
     *  summary="TODO create items",
     *
     *  @OA\Parameter(name="itemType",
     *    in="path",
     *    required=true,
     *     description="games / software / swiches / cases",
     *  ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *       ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Error: Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Error: Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Error: Forbidden / Недостаточно прав для выполнения запроса"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Error: Resource Not Found"
     *      )
     * )
     */
    //TODO необходимы входящие параметры
    public function create(): JsonResponse
    {
        return response()->json(['error' => 'Метод API находится в разработке!', 'status' => 400], 400);
    }

    /**
     * @OA\Put (tags={"Personal Area: Sales"}, path="/personal-area/sales/{itemType}/{itemId}",
     *  security={{"bearer_token":{}}},
     *  operationId="sales_update",
     *  summary="update items",
     *
     *  @OA\Parameter(name="itemType",
     *    in="path",
     *    required=true,
     *     description="games / software / swiches / cases",
     *  ),
     *  @OA\Parameter(name="itemId",
     *    in="path",
     *    required=true,
     *  ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/SalesUpdateRequest")
     *      ),
     *
     *  @OA\Response(
     *    response=200,
     *    description="Successful operation",
     *  ),
     * ),
     */
    public function update(SalesUpdateRequest $request, $itemType, $itemId): JsonResponse
    {
        if (isset($request['archive'])) {
            // определяем тип запроса
            $itemTypes = TypesController::itemTypes();
            if (isset($itemTypes[$itemType])) {
                $itemType = $itemTypes[$itemType];
            } else {
                return response()->json(['error' => 'Тип ' . $itemType . ' отсутствует в апи!', 'status' => 400], 400);
            }

            try {
                $sellerId = $request->user()->seller->id;
                // ищем ключи
                $model = $itemType['modelKey']::select('id')->where(['item_id' => $itemId, 'seller_id' => $sellerId, 'bought' => 0]);
                $result = ($request['archive']) ? $model->withoutTrashed()->delete() : $model->onlyTrashed()->restore();
                // проверяем наличие изменений
                if ($result > 0) {
                    $message = ($request['archive']) ? 'Вы добавили ключи в архив.' : 'Вы убрали ключи из архива и выставили вновь на активные продажи!';
                    return response()->json(['message' => $message, 'status' => 201], 201);
                } else {
                    $message = ($request['archive']) ? 'Эти ключи уже в архиве.' : 'Эти ключи не в архиве!';
                    return response()->json(['warning' => $message,
                        'status' => 400], 400);
                }
            } catch (\Throwable) {
                return response()->json(['error' => 'Произошла ошибка. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

        }

        return response()->json(['error' => 'Метод API находится в разработке!', 'status' => 400], 400);
    }

    /**
     * @OA\Delete (tags={"Personal Area: Sales"}, path="/personal-area/sales/{itemType}/{itemId}",
     *   security={{"bearer_token":{}}},
     *  operationId="sales_delete",
     *  summary="delete items",
     *
     *  @OA\Parameter(name="itemType",
     *    in="path",
     *    required=true,
     *     description="games / software / swiches / cases",
     *  ),
     *  @OA\Parameter(name="itemId",
     *    in="path",
     *    required=true,
     *  ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     * )
     */
    public function delete(Request $request, $itemType, $itemId): JsonResponse
    {
        // определяем тип запроса
        $itemTypes = TypesController::itemTypes();
        if (isset($itemTypes[$itemType])) {
            $itemType = $itemTypes[$itemType];
        } else {
            return response()->json(['error' => 'Тип ' . $itemType . ' отсутствует в апи!', 'status' => 400], 400);
        }

        try {
            $sellerId = $request->user()->seller->id;
            // ищем ключи
            $result = $itemType['modelKey']::select('id')->where(['item_id' => $itemId, 'seller_id' => $sellerId, 'bought' => 0])->forceDelete();
            // проверяем наличие изменений
            if ($result > 0) {
                $message = 'Удалено';
                return response()->json(['message' => $message, 'status' => 201], 201);
            } else {
                return response()->json(['warning' => 'Позиция не найдена.',
                    'status' => 400], 400);
            }
        } catch (\Throwable) {
            return response()->json(['error' => 'Произошла ошибка. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

}
