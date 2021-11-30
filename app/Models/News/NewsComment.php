<?php

namespace App\Models\News;

use App\Models\Concerns\UseUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class NewsComment extends Model
{
    use HasFactory, SoftDeletes, UseUuid;

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'id', 'news_id')->withTrashed();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
