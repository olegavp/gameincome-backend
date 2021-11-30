<?php

namespace App\Http\Controllers\User\Appeals;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Appeals\ShowAppealMessagesResource;
use App\Models\User\PersonalArea\Appeals\DisputeAppeal;
use App\Models\User\PersonalArea\Appeals\GeneralAppeal;
use App\Models\User\PersonalArea\Appeals\PartnershipAppeal;
use App\Models\User\PersonalArea\Appeals\TechSupportAppeal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class GetMessagesController extends Controller
{
    public function getMessages($appeal, Request $request): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            $type = explode('/', parse_url($request->url())['path']);

            if ($type[4] === 'dispute')
            {
                $query = DisputeAppeal::query();
            }
            elseif ($type[4] === 'general')
            {
                $query = GeneralAppeal::query();
            }
            elseif ($type[4] === 'partnership')
            {
                $query = PartnershipAppeal::query();
            }
            elseif ($type[4] === 'tech-support')
            {
                $query = TechSupportAppeal::query();
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения обращений, вы не выбрали тип обращений. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $userId = $request->user()->id;
            $messages = $query->with('messages', 'messages.user', 'messages.admin')->where(['id' => $appeal, 'user_id' => $userId])->select('id')->get();
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
}
