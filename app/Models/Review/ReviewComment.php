<?php

namespace App\Models\Review;

use App\Models\Concerns\UseUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class ReviewComment extends Model
{
    use HasFactory, useUuid;

    protected $table = 'reviews_comments';


    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class, 'id', 'review_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
