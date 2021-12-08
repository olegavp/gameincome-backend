<?php

namespace App\Http\Controllers\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reviews\ReviewsResource;
use App\Models\Item\Game;
use App\Models\Item\Software;
use App\Models\Review\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class GetReviewsController extends Controller
{
    public function get(): JsonResponse|AnonymousResourceCollection
    {
        try {
            $reviews = Review::query()->with('writer', 'comments', 'views')->orderByDesc('created_at')->paginate(5);

            $reviewsGames = [];
            $reviewsSoftware = [];
            foreach ($reviews as $review) {
                if ($review->item_type === 'game') {
                    array_push($reviewsGames, $review->item_id);
                } elseif ($review->item_type === 'software') {
                    array_push($reviewsSoftware, $review->item_id);
                }
            }

            if (count($reviewsGames) > 0) {
                $games = Game::query()->whereIn('id', $reviewsGames)->get();
                foreach ($reviews as $review) {
                    foreach ($games as $game) {
                        if ($review->item_id === $game->id) {
                            $review['item'] = $game;
                        }
                    }
                }
            }

            if (count($reviewsSoftware) > 0) {
                $software = Software::query()->whereIn('id', $reviewsSoftware)->get();
                foreach ($reviews as $review) {
                    foreach ($software as $oneSoftware) {
                        if ($review->item_id === $oneSoftware->id) {
                            $review['item'] = $oneSoftware;
                        }
                    }
                }
            }

            return ReviewsResource::collection($reviews)
                ->additional(['status' => 200]);
        } catch (\Throwable) {
            return response()->json([
                'error' => 'Произошла ошибка при загрузке всех обзоров. Попробуйте перезагрузить страницу, если ошибка остаётся, то обратитесь в поддержку, Спасибо!',
                'status' => 400,
            ], 400);
        }
    }
}
