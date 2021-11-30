<?php

namespace App\Http\Resources\Seller\PublicProfile;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class SellerProfileResource extends JsonResource
{
    #[ArrayShape(['likes' => "mixed", 'dislikes' => "mixed", 'countFeedbacks' => "mixed", 'userComments' => "array"])]
    private function feedbacks(): array
    {
        if ($this->feedbacks->isEmpty())
        {
            return ['likes' => 0, 'dislikes' => 0, 'countFeedbacks' => 0, 'userComments' => null];
        }

        $eloquentFeedbacks = $this->feedbacks;
        $likes = $eloquentFeedbacks->where('rate', 1)->count();
        $dislikes = $eloquentFeedbacks->where('rate', 0)->count();

        $users = $eloquentFeedbacks->map->user;
        $sellerFeedbacks = array();
        foreach ($eloquentFeedbacks as $key => $feedback)
        {
            foreach ($users as $user)
            {
                if ($feedback->user_id === $user->id)
                {
                    $sellerFeedbacks[$key]['nickname'] = $user->nickname;
                    $sellerFeedbacks[$key]['avatar'] = $user->avatar;
                    $sellerFeedbacks[$key]['commentId'] = $feedback->id;
                    $sellerFeedbacks[$key]['comment'] = $feedback->comment;
                    $sellerFeedbacks[$key]['time'] = $feedback->created_at;
                }
            }
        }
        return ['likes' => $likes, 'dislikes' => $dislikes, 'countFeedbacks' => $eloquentFeedbacks->count(), 'userComments' => $sellerFeedbacks];
    }


    #[ArrayShape(['name' => "mixed", 'surname' => "mixed", 'nickname' => "mixed", 'avatar' => "mixed", 'purchases' => "mixed", 'items' => "mixed", 'feedbacks' => "mixed", 'reviews' => "mixed"])]
    public function toArray($request): array
    {
        return [
            'name' => $this->user->name,
            'surname' => $this->user->surname,
            'nickname' => $this->user->nickname,
            'avatar' => $this->user->avatar,
            'purchases' => $this->gamePurchases->count() + $this->softwarePurchases->count(),
            'items' => $this->games->count() + $this->software->count(),
            'feedbacks' => $this->feedbacks(),
            'reviews' => $this->reviews->count()
        ];
    }
}
