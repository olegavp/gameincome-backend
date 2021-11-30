<?php

namespace App\Models\Purchase;

use App\Models\Item\Game;
use App\Models\Item\GameKey;
use App\Models\Seller\Seller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\UseUuid as Uuid;
use Illuminate\Database\Eloquent\Relations\HasOne;


class GamePurchase extends Model
{
    use HasFactory, Uuid;

    public function key(): HasOne
    {
        return $this->hasOne(GameKey::class, 'id', 'key_id');
    }

    public function item(): HasOne
    {
        return $this->hasOne(Game::class, 'id', 'item_id');
    }


    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class, 'id', 'seller_id');
    }
}
