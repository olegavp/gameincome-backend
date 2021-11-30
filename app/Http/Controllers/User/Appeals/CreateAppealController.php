<?php

namespace App\Http\Controllers\User\Appeals;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PersonalArea\Appeals\CreateAppealsRequest;
use App\Models\User\PersonalArea\Appeals\DisputeAppeal;
use App\Models\User\PersonalArea\Appeals\DisputeAppealMessage;
use App\Models\User\PersonalArea\Appeals\GeneralAppeal;
use App\Models\User\PersonalArea\Appeals\GeneralAppealMessage;
use App\Models\User\PersonalArea\Appeals\PartnershipAppeal;
use App\Models\User\PersonalArea\Appeals\PartnershipAppealMessage;
use App\Models\User\PersonalArea\Appeals\TechSupportAppeal;
use App\Models\User\PersonalArea\Appeals\TechSupportAppealMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class CreateAppealController extends Controller
{
    public function createAppeal(CreateAppealsRequest $request): JsonResponse
    {
        try
        {
            $type = explode('/', parse_url($request->url())['path']);

            if ($type[4] === 'dispute')
            {
                $query = DisputeAppeal::class;
                $query2 = DisputeAppealMessage::class;
            }
            elseif ($type[4] === 'general')
            {
                $query = GeneralAppeal::class;
                $query2 = GeneralAppealMessage::class;
            }
            elseif ($type[4] === 'partnership')
            {
                $query = PartnershipAppeal::class;
                $query2 = PartnershipAppealMessage::class;
            }
            elseif ($type[4] === 'tech-support')
            {
                $query = TechSupportAppeal::class;
                $query2 = TechSupportAppealMessage::class;
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время создания обращения, вы не выбрали тип обращения. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            DB::transaction(function () use ($request, $query, $query2)
            {
                $appeal = new $query;
                $appeal->number = rand(100000,999999);
                $appeal->user_id = $request->user()->id;
                $appeal->theme = $request->theme;
                $appeal->save();

                $message = new $query2;
                $message->appeal_id = $appeal->id;
                $message->user_id = $request->user()->id;
                $message->text = $request->text;
                $message->save();
            });

            return response()->json(['message' => 'Ваше обращение зарегистрировано, поддержка ответит на него в порядке очереди!',
                'status' => 201], 201);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время создания обращения. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
