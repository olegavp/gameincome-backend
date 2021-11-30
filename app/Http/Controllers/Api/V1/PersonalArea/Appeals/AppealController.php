<?php

namespace App\Http\Controllers\Api\V1\PersonalArea\Appeals;

use App\Http\Controllers\Api\V1\Types\TypesController;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\PersonalArea\Appeals\CreateAppealsRequest;
use App\Http\Requests\User\PersonalArea\Purchases\CreateDisputeRequest;
use App\Http\Resources\User\Appeals\ShowAppealsResource;
use App\Models\Item\GameKey;
use App\Models\Item\SoftwareKey;
use App\Models\Purchase\GamePurchase;
use App\Models\Purchase\SoftwarePurchase;
use App\Models\User\PersonalArea\Appeals\DisputeAppeal;
use App\Models\User\PersonalArea\Appeals\DisputeAppealMessage;
use App\Models\User\PersonalArea\Appeals\GeneralAppeal;
use App\Models\User\PersonalArea\Appeals\GeneralAppealMessage;
use App\Models\User\PersonalArea\Appeals\PartnershipAppeal;
use App\Models\User\PersonalArea\Appeals\PartnershipAppealMessage;
use App\Models\User\PersonalArea\Appeals\TechSupportAppeal;
use App\Models\User\PersonalArea\Appeals\TechSupportAppealMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;


class AppealController extends Controller
{

    /**
     * @OA\Get (tags={"Personal Area: Appeals"}, path="/personal-area/appeals/{appealType}",
     *  security={{"bearer_token":{}}},
     *  operationId="personal_area_appeals",
     *  summary="Personal Area Appeals",
     *
     *  @OA\Parameter(name="appealType",
     *    in="path",
     *     required=true,
     *     description="general / dispute / partnership / tech-support",
     *     example="general",
     *  ),
     *
     *       @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/ShowAppealsResource")
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
    public function appeals(Request $request, $appealType): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            // определяем тип запроса
            $appealTypes = TypesController::appealTypes();
            if (isset($appealTypes[$appealType])){
                $appealType = $appealTypes[$appealType];
                return (ShowAppealsResource::collection($appealType['model']::with('messages')->where('user_id', $request->user()->id)->paginate(10)))
                    ->additional(['status' => 200]);
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения обращений, вы не выбрали тип обращений. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отображения обращений. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

    /**
     * @OA\Post (tags={"Personal Area: Appeals"}, path="/personal-area/appeals/{appealType}",
     *   security={{"bearer_token":{}}},
     *  operationId="personal_area_appeals_create",
     *  summary="Create Appeals",
     *
     *  @OA\Parameter(name="appealType",
     *    in="path",
     *     required=true,
     *     description="general / dispute / partnership / tech-support",
     *  ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CreateAppealsRequest")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *       ),
     *
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
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Error: Conflict / Не указаны или указаны некорректно обязательные для запроса dispute параметры: itemType и keyId"
     *      ),
     * )
     */
    public function create(CreateAppealsRequest $request, $appealType): JsonResponse
    {
        try
        {
            // check type request
            $appealTypes = TypesController::appealTypes();
            if (isset($appealTypes[$appealType])){
                $appealType = $appealTypes[$appealType];
                // for disputes
                $key = null;
                $itemTypes = TypesController::itemTypes();
                if ($appealType['name'] == 'dispute' && isset($itemTypes[$request['itemType']]) && isset($request['keyId'])){
                    $itemType = $itemTypes[$request['itemType']];
                    // проверка наличия ключа в покупках
                    if ($itemType['modelPurchase']::where(['user_id' => $request->user()->id, 'key_id' => $request['keyId']])->exists()){
                        $key = $itemType['modelKey']::where(['id' => $request['keyId']])->first();
                    }
                    else{
                        return response()->json(['error' => 'Произошла ошибка, в ваших покупках нет данного ключа!', 'status' => 400], 400);
                    }
                }
                elseif ($appealType['name'] == 'dispute'){
                    return response()->json(['error' => 'Произошла ошибка во время создания обращения. Не указаны или указаны некорректно обязательные для запроса dispute параметры: itemType и keyId',
                        'status' => 409], 409);
                }
                // request to db
                DB::transaction(function () use ($request, $appealType, $key)
                {

                    $appeal = new $appealType['model'];
                    $appeal->number = rand(100000,999999);
                    $appeal->user_id = $request->user()->id;
                    if ($appealType['name'] == 'dispute'){
                        $appeal->item_type = $request['itemType'];
                        $appeal->key_id = $key->id;
                        $appeal->seller_id = $key->seller_id;
                    }
                    else{
                        $appeal->theme = $request->theme;
                    }
                    $appeal->save();

                    $appealMessage = $appealType['model'].'Message';
                    $message = new $appealMessage;
                    $message->appeal_id = $appeal->id;
                    $message->user_id = $request->user()->id;
                    $message->text = $request->text;
                    // image if exists
                    if( $request->hasFile('image')){
                        $filenameWithExt = $request->file('image')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('image')->getClientOriginalExtension();
                        $fileNameToStore = "image/".$filename."_".time().".".$extension;
                        $request->file('image')->storeAs('public/support-files', $fileNameToStore);
                        $message->path_to_image = env('URL_FOR_FILES') . '/storage/support-files/' . $fileNameToStore;
                    }
                    $message->save();
                });

                $message = 'Ваше обращение зарегистрировано, поддержка ответит на него в порядке очереди!';
                if ($appealType['name'] == 'dispute') $message = 'Следите за ходом спора в разделе Обращений в вашем Личном кабинете!';
                return response()->json(['message' => $message, 'status' => 201], 201);
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время создания обращения, вы не выбрали тип обращения. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время создания обращения. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

}
