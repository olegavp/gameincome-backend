<?php

namespace App\Http\Resources\News;

use App\Http\Resources\CommentsResource;
use App\Models\News\NewsComment;
use Illuminate\Http\Resources\Json\JsonResource;


class NewsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'newsId' => $this->id,
            'newsName' => $this->name,
            'newsDescriptionOn3Words' => $this->description_on_3_words,
            'newsSmallDescription' => $this->small_description,
            'newsDescription' => $this->description,
            'newsType' => $this->type,
            'newsRelation' => $this->relation,
            'newsBackground' => $this->background,
            'newsCreatedAt' => $this->created_at,
            'newsUpdatedAt' => $this->updated_at,
            'newsComments' => CommentsResource::collection(NewsComment::query()->with('user')->whereIn('id', $this->comments->pluck('id'))->withTrashed()->get())
        ];
    }
}
