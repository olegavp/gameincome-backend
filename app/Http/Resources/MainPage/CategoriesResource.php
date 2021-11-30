<?php

namespace App\Http\Resources\MainPage;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;


class CategoriesResource extends JsonResource
{
    #[ArrayShape(['name' => "\Illuminate\Database\Eloquent\HigherOrderBuilderProxy|\Illuminate\Support\HigherOrderCollectionProxy|mixed", 'background' => "mixed"])]
    public function toArray($request): array
    {
        return [
            'name' => $this->category->name,
            'background' => $this->background
        ];
    }
}
