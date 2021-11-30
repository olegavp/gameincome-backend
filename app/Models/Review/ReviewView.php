<?php

namespace App\Models\Review;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewView extends Model
{
    use HasFactory;

    protected $table = 'reviews_views';

    public $timestamps = false;
}
