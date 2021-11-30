<?php

namespace App\Http\Controllers\User\Appeals;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\Appeals\ShowAppealsResource;
use App\Models\User\PersonalArea\Appeals\DisputeAppeal;
use App\Models\User\PersonalArea\Appeals\GeneralAppeal;
use App\Models\User\PersonalArea\Appeals\PartnershipAppeal;
use App\Models\User\PersonalArea\Appeals\TechSupportAppeal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;


class GetAppealsController extends Controller
{
    public function getAppeals(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            $type = explode('/', parse_url($request->url())['path']);

            if (isset($type[4])){
                $query = null;
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

                if ($query != null){
                    return (ShowAppealsResource::collection($query->with('messages')->where('user_id', $request->user()->id)->paginate(10)))
                        ->additional(['status' => 200]);
                }
                else
                {
                    return response()->json(['error' => 'Произошла ошибка во время отображения обращений, вы не выбрали тип обращений. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                        'status' => 400], 400);
                }
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
}
