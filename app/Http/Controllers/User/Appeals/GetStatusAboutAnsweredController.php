<?php

namespace App\Http\Controllers\User\Appeals;

use App\Http\Controllers\Controller;
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


class GetStatusAboutAnsweredController extends Controller
{
    public function getStatus($appeal, Request $request): JsonResponse
    {
        try
        {
            $type = explode('/', parse_url($request->url())['path']);

            if ($type[5] === 'dispute')
            {
                $query = DisputeAppeal::query();
                $query2 = DisputeAppealMessage::query();
            }
            elseif ($type[5] === 'general')
            {
                $query = GeneralAppeal::query();
                $query2 = GeneralAppealMessage::query();
            }
            elseif ($type[5] === 'partnership')
            {
                $query = PartnershipAppeal::query();
                $query2 = PartnershipAppealMessage::query();
            }
            elseif ($type[5] === 'tech-support')
            {
                $query = TechSupportAppeal::query();
                $query2 = TechSupportAppealMessage::query();
            }
            else
            {
                return response()->json(['error' => 'Произошла ошибка во время отображения статуса обращения, вы не выбрали тип обращений. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                    'status' => 400], 400);
            }

            $userId = $request->user()->id;

            $messages = $query2->where(['user_id' => $userId, 'appeal_id' => $appeal])->select('admin_id', 'created_at')->orderByDesc('created_at')->get();

            if ($messages[0]->admin_id !== null)
                $answered = 1;
            else
                $answered = 0;

            $query->where('id', $appeal)->update(['answered' => $answered]);

            return response()->json(['appealStatus' => $answered,
                'status' => 200], 200);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время загрузки статуса обращения. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
