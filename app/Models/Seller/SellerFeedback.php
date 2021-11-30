<?php

namespace App\Models\Seller;

use App\Models\Item\GameKey;
use App\Models\Item\SoftwareKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\UseUuid as Uuid;
use Illuminate\Database\Eloquent\Relations\HasOne;


class SellerFeedback extends Model
{
    use HasFactory, Uuid;

    protected $table = 'sellers_feedbacks';


    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'id', 'seller_id');
    }


    public function user(): HasOne
    {
        return $this->HasOne(User::class, 'id', 'user_id');
    }


    public function gameKey(): HasOne
    {
        return $this->HasOne(GameKey::class, 'id', 'key_id');
    }


    public function softwareKey(): HasOne
    {
        return $this->HasOne(SoftwareKey::class, 'id', 'key_id');
    }
}
