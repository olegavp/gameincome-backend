<?php

namespace App\Models\Item;

use App\Models\Concerns\UseUuid;
use App\Models\Seller\Seller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class SoftwareKey extends Model
{
    use HasFactory, softdeletes, useUuid;

    public function item(): HasOne
    {
        return $this->hasOne(Software::class, 'id', 'item_id');
    }

    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class, 'id', 'seller_id');
    }
}
