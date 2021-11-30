<?php

namespace App\Models\MainPage;

use App\Models\AdminUser;
use App\Models\Concerns\UseUuid;
use App\Models\Review\ReviewComment;
use App\Models\Review\ReviewView;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Review extends Model
{
    use HasFactory, useUuid;

    protected $table = 'main_page_reviews';

    public $timestamps = false;


    public function writer(): HasOne
    {
        return $this->HasOne(AdminUser::class, 'id', 'writer_id');
    }

    public function comments(): HasMany
    {
        return $this->HasMany(ReviewComment::class, 'review_id', 'id')->orderBy('created_at', 'DESC');
    }

    public function views(): HasMany
    {
        return $this->HasMany(ReviewView::class, 'review_id', 'id');
    }
}
