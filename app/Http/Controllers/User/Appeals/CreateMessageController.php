<?php

namespace App\Http\Controllers\User\Appeals;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PersonalArea\Appeals\CreateMessageAppealsRequest;
use App\Http\Resources\User\Appeals\ShowAppealMessagesResource;
use App\Models\User\PersonalArea\Appeals\DisputeAppeal;
use App\Models\User\PersonalArea\Appeals\DisputeAppealMessage;
use App\Models\User\PersonalArea\Appeals\GeneralAppeal;
use App\Models\User\PersonalArea\Appeals\GeneralAppealMessage;
use App\Models\User\PersonalArea\Appeals\PartnershipAppeal;
use App\Models\User\PersonalArea\Appeals\PartnershipAppealMessage;
use App\Models\User\PersonalArea\Appeals\TechSupportAppeal;
use App\Models\User\PersonalArea\Appeals\TechSupportAppealMessage;
use Illuminate\Support\Facades\DB;


class CreateMessageController extends Controller
{
    public function createMessage($appeal, CreateMessageAppealsRequest $request)
    {
        try
        {
            $type = explode('/', parse_url($request->url())['path']);

            if ($type[4] === 'dispute')
            {
                $query = DisputeAppeal::query();
                $query2 = DisputeAppealMessage::class;
            }
            elseif ($type[4] === 'general')
            {
                $query = GeneralAppeal::query();
                $query2 = GeneralAppealMessage::class;
            }
            elseif ($type[4] === 'partnership')
            {
                $query = PartnershipAppeal::query();
                $query2 = PartnershipAppealMessage::class;
            }
            elseif ($type[4] === 'tech-support')
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

            DB::transaction(function () use ($query, $query2, $appeal, $userId, $request)
            {
                $message = new $query2;
                $message->appeal_id = $appeal;
                $message->user_id = $userId;
                $message->text = $request->text;
                $message->save();

                $query->where('id', $appeal)->update(['answered' => 0]);
            });

            $messages = $query->with('messages', 'messages.user', 'messages.admin')->where(['id' => $appeal, 'user_id' => $userId])->select('id')->get();
            $messages = $messages[0]->messages->sortByDesc('created_at');
            if ($messages->isEmpty())
            {
                return response()->json(['warning' => 'Данное обращение не является вашим!',
                    'status' => 400], 400);
            }

            return (ShowAppealMessagesResource::collection($messages))
                ->additional(['answered' => 0, 'status' => 201]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время отправки сообщения. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
