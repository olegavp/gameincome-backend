<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Http\Resources\News\BigNewsResource;
use App\Http\Resources\News\SmallNewsResource;
use App\Models\News\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class GetBigAndSmallNewsController extends Controller
{
    public function getSmall(): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            return (SmallNewsResource::collection(News::query()->where('type', 'small')->orderByDesc('created_at')->get()))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при загрузке всех малых новостей. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }


    public function getBig(): JsonResponse|AnonymousResourceCollection
    {
        try
        {
            return (BigNewsResource::collection(News::query()->where('type', 'big')->orderByDesc('created_at')->paginate(6)))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при загрузке всех больших новостей. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
