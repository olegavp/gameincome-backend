<?php

namespace App\Models\MainPage;

use App\Models\Concerns\UseUuid as Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory, Uuid;

    protected $table = 'main_page_slider';

    public $timestamps = false;
}
