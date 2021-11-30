<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Http\Resources\News\NewsResource;
use App\Http\Resources\News\SmallNewsResource;
use App\Models\News\News;
use Illuminate\Http\JsonResponse;


class ToNewsController extends Controller
{
    public function get(News $news): NewsResource|JsonResponse
    {
        try
        {
            $relation = $news->relation;

            if ($relation === null)
            {
                $relationNews = null;
            }
            else
            {
                $relationNews = News::query()->where('relation', $relation)->where('id', '!=', $news->id)->orderByDesc('created_at')->get();
            }

            $relationNews = SmallNewsResource::collection($relationNews);
            return (new NewsResource($news->load('comments')))
                ->additional([
                    'relation' => $relationNews,
                    'status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при переходе на новость. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
