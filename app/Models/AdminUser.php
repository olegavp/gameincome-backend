<?php

namespace App\Models;

use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AdminUser extends Model
{
    use HasFactory, useUuid;

}
