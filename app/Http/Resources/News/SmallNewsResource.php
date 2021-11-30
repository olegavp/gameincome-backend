<?php

namespace App\Http\Resources\News;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class SmallNewsResource extends JsonResource
{
    #[ArrayShape(['newsId' => "mixed", 'newsName' => "mixed", 'newsDescriptionOn3Words' => "mixed", 'newsSmallDescription' => "mixed", 'newsDescription' => "mixed", 'newsBackground' => "mixed", 'newsCreatedAt' => "mixed"])]
    public function toArray($request): array
    {
        return [
            'newsId' => $this->id,
            'newsName' => $this->name,
            'newsDescriptionOn3Words' => $this->description_on_3_words,
            'newsSmallDescription' => $this->small_description,
            'newsDescription' => $this->description,
            'newsBackground' => $this->small_background,
            'newsCreatedAt' => $this->created_at,
        ];
    }
}
