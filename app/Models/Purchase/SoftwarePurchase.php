<?php

namespace App\Models\Purchase;

use App\Models\Item\Software;
use App\Models\Item\SoftwareKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\UseUuid as Uuid;
use Illuminate\Database\Eloquent\Relations\HasOne;


class SoftwarePurchase extends Model
{
    use HasFactory, Uuid;

    public function key(): HasOne
    {
        return $this->hasOne(SoftwareKey::class, 'id', 'key_id');
    }

    public function item(): HasOne
    {
        return $this->hasOne(Software::class, 'id', 'item_id');
    }
}
