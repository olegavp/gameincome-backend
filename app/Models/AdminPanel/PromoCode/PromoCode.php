<?php

namespace App\Models\AdminPanel\PromoCode;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\UseUuid as Uuid;

class PromoCode extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    protected $fillable = [
        'name',
        'count',
        'money',
        'finish_time'
    ];
}
