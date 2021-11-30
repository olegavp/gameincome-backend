<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\MakeCommentRequest;
use App\Http\Resources\CommentsResource;
use App\Models\News\NewsComment;
use Illuminate\Http\JsonResponse;


class CreateCommentController extends Controller
{
    public function create(MakeCommentRequest $request): CommentsResource|JsonResponse
    {
        try
        {
            $comment = new NewsComment;
            $comment->news_id = $request->newsId;
            $comment->user_id = $request->user()->id;
            $comment->parent_id = $request->parentId;
            $comment->comment_text = $request->text;
            $comment->save();

            return (new CommentsResource($comment))
                ->additional([
                    'message' => 'Вы успешно добавили комментарий к данной новости!',
                    'status' => 201]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка во время добавления комментария к новости. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
