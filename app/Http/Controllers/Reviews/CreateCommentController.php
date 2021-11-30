<?php

namespace App\Http\Controllers\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\MakeReviewCommentRequest;
use App\Http\Resources\CommentsResource;
use App\Models\Review\ReviewComment;
use Illuminate\Http\JsonResponse;


class CreateCommentController extends Controller
{
    public function create(MakeReviewCommentRequest $request): CommentsResource|JsonResponse
    {
        try
        {
            $comment = new ReviewComment();
            $comment->review_id = $request->reviewId;
            $comment->user_id = $request->user()->id;
            $comment->parent_id = $request->parentId;
            $comment->comment_text = $request->text;
            $comment->save();

            return (new CommentsResource($comment))
                ->additional([
                    'message' => 'Вы успешно добавили комментарий к данному обзору!',
                    'status' => 201]);
        }
        catch (\Throwable $e)
        {
            return response()->json(['error' => 'Произошла ошибка во время добавления комментария к обзору. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!' . $e,
                'status' => 400], 400);
        }
    }
}
