<?php

namespace App\Http\Resources\MainPage;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class ServicesResource extends JsonResource
{
    #[ArrayShape(['id' => "mixed", 'slug' => "mixed", 'background' => "mixed"])]
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'background' => $this->background
        ];
    }
}
