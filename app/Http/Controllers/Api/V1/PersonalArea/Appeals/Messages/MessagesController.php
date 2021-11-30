<?php

namespace App\Http\Controllers\Api\V1\PersonalArea\Appeals\Messages;

use App\Http\Controllers\Api\V1\Types\TypesController;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\PersonalArea\Appeals\CreateAppealsRequest;
use App\Http\Requests\User\PersonalArea\Appeals\CreateMessageAppealsRequest;
use App\Http\Resources\User\Appeals\ShowAppealMessagesResource;
use App\Http\Resources\User\Appeals\ShowAppealsResource;
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


class MessagesController extends Controller
{
    /**
     * @OA\Get (tags={"Personal Area: Appeals"}, path="/personal-area/appeals/{appealType}/{appealId}/messages",
     *  security={{"bearer_token":{}}},
     *  operationId="personal_area_appeals_messages",
     *  summary="Personal Area Appeal Messages",
     *
     *  @OA\Parameter(name="appealType",
     *    in="path",
     *     required=true,
     *     description="general / dispute / partnership / tech-support",
     *  ),
     *  @OA\Parameter(name="appealId",
     *    in="path",
     *     required=true,
     *  ),
     *
     *       @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(ref="#/components/schemas/ShowAppealMessagesResource")
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
    public function messages(Request $request, $appealType, $appealId): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            if ($appealType === 'dispute')
            {
                $query = DisputeAppeal::query();
            }
            elseif ($appealType === 'general')
            {
                $query = GeneralAppeal::query();
            }
            elseif ($appealType === 'partnership')
            {
                $query = PartnershipAppeal::query();
            }
            elseif ($appealType === 'tech-support')
            {
                $query = TechSupportAppeal::query();
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения обращений, вы не выбрали тип обращений. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $userId = $request->user()->id;
            $messages = $query->with('messages', 'messages.user', 'messages.admin')->where(['id' => $appealId, 'user_id' => $userId])->select('id')->get();
            $messages = $messages[0]->messages->sortByDesc('created_at');
            if ($messages->isEmpty())
            {
                return response()->json(['warning' => 'Данное обращение не является вашим!',
                    'status' => 400], 400);
            }

            return (ShowAppealMessagesResource::collection($messages))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время загрузки новых сообщений. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

    /**
     * @OA\Post (tags={"Personal Area: Appeals"}, path="/personal-area/appeals/{appealType}/{appealId}/message",
     *   security={{"bearer_token":{}}},
     *  operationId="personal_area_appeals_create_message",
     *  summary="Create Appeals message",
     *
     *  @OA\Parameter(name="appealType",
     *    in="path",
     *     required=true,
     *     description="general / dispute / partnership / tech-support",
     *  ),
     *  @OA\Parameter(name="appealId",
     *    in="path",
     *     required=true,
     *  ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CreateMessageAppealsRequest")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *          @OA\JsonContent(ref="#/components/schemas/ShowAppealMessagesResource")
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
     *          description="Error: Conflict / Не указаны или указаны некорректно обязательные для запроса dispute параметры: itemType и id"
     *      ),
     * )
     */
    public function create(CreateMessageAppealsRequest $request, $appealType, $appealId): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            if ($appealType === 'dispute')
            {
                $query = DisputeAppeal::query();
                $query2 = DisputeAppealMessage::class;
            }
            elseif ($appealType === 'general')
            {
                $query = GeneralAppeal::query();
                $query2 = GeneralAppealMessage::class;
            }
            elseif ($appealType === 'partnership')
            {
                $query = PartnershipAppeal::query();
                $query2 = PartnershipAppealMessage::class;
            }
            elseif ($appealType === 'tech-support')
            {
                $query = TechSupportAppeal::query();
                $query2 = TechSupportAppealMessage::class;
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время отправки сообщения, вы не выбрали тип обращений. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $userId = $request->user()->id;

            DB::transaction(function () use ($query, $query2, $appealId, $userId, $request)
            {
                $message = new $query2;
                $message->appeal_id = $appealId;
                $message->user_id = $userId;
                $message->text = $request->text;
                $message->save();

                $query->where('id', $appealId)->update(['answered' => 0]);
            });

            $messages = $query->with('messages', 'messages.user', 'messages.admin')->where(['id' => $appealId, 'user_id' => $userId])->select('id')->get();
            $messages = $messages[0]->messages->sortByDesc('created_at');
            if ($messages->isEmpty())
            {
                return response()->json(['warning' => 'Данное обращение не является вашим!',
                    'status' => 400], 400);
            }

            return (ShowAppealMessagesResource::collection($messages))
                ->additional(['answered' => 0, 'status' => 201], 201);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отправки сообщения. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }

}
