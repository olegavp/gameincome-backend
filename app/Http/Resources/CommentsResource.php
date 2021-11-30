<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class CommentsResource extends JsonResource
{
    #[ArrayShape(['id' => "mixed", 'userId' => "mixed", 'avatar' => "mixed", 'nickname' => "mixed", 'parentId' => "mixed", 'commentText' => "mixed|string", 'createdAt' => "mixed", 'updatedAt' => "mixed"])]
    public function toArray($request): array
    {

        if ($this->deleted_at == !null)
        {
            $text = 'Этот комментарий был удалён пользователем или за нарушение правил площадки.';
        }
        else
        {
            $text = $this->comment_text;
        }

        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'avatar' => $this->user->avatar,
            'nickname' => $this->user->nickname,
            'parentId' => $this->parent_id,
            'commentText' => $text,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
