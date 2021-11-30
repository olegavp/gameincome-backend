<?php

namespace App\Models\MainPage;

use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, useUuid;

    protected $table = 'services';

    public $timestamps = false;
}
