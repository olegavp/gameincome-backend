<?php

namespace App\Models\Item;

use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory, useUuid;

    public $timestamps = false;
}
