<?php

namespace App\Http\Controllers\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reviews\ReviewResource;
use App\Models\Item\Game;
use App\Models\Item\Software;
use App\Models\Review\Review;
use App\Models\Review\ReviewView;
use Illuminate\Http\JsonResponse;


class ToReviewController extends Controller
{
    public function get(Review $review): JsonResponse|ReviewResource
    {
        try
        {
            $reviewView = ReviewView::query()->where('review_id', $review->id)->first();
            $reviewView->count = $reviewView->count + 1;
            $reviewView->save();

            $item = null;
            if ($review->item_type === 'game')
            {
                $item = Game::query()->where('id', $review->item_id)->first();
            }
            elseif ($review->item_type === 'software')
            {
                $item = Software::query()->where('id', $review->item_id)->first();
            }
            $review['item'] = $item;

            return (new ReviewResource($review->load('writer', 'comments', 'views')))
                ->additional(['status' => 200]);
        }
        catch (\Throwable)
        {
            return response()->json(['error' => 'Произошла ошибка при переходе на обзор. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400], 400);
        }
    }
}
