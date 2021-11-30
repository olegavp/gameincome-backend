<?php

namespace App\Models\MainPage;

use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insert extends Model
{
    use HasFactory, useUuid;

    protected $table = 'main_page_inserts';

    public $timestamps = false;
}
