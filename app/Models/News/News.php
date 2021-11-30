<?php

namespace App\Models\News;

use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class News extends Model
{
    use HasFactory, SoftDeletes, UseUuid;

    public function comments(): HasMany
    {
        return $this->HasMany(NewsComment::class, 'news_id', 'id')->withTrashed()->orderBy('created_at', 'ASC');
    }
}
