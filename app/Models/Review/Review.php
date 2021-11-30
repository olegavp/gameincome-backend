<?php

namespace App\Models\Review;

use App\Models\AdminUser;
use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Review extends Model
{
    use HasFactory, useUuid;


    public function writer(): HasOne
    {
        return $this->HasOne(AdminUser::class, 'id', 'writer_id');
    }

    public function comments(): HasMany
    {
        return $this->HasMany(ReviewComment::class, 'review_id', 'id')->orderBy('created_at', 'DESC');
    }

    public function views(): HasOne
    {
        return $this->HasOne(ReviewView::class, 'review_id', 'id');
    }
}
