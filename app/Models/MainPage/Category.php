<?php

namespace App\Models\MainPage;

use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Category extends Model
{
    use HasFactory, useUuid;

    protected $table = 'main_page_categories';

    public function category(): HasOne
    {
        return $this->HasOne(\App\Models\Item\Category::class, 'id', 'category_id');
    }
}
