<?php

namespace App\Models\MainPage;

use App\Models\Concerns\UseUuid as Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hit extends Model
{
    use HasFactory, Uuid;

    protected $table = 'main_page_hits';

    public $timestamps = false;
}
