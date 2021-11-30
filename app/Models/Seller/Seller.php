<?php

namespace App\Models\Seller;

use App\Models\Item\GameKey;
use App\Models\Item\SoftwareKey;
use App\Models\Purchase\GamePurchase;
use App\Models\Purchase\SoftwarePurchase;
use App\Models\Review\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\UseUuid as Uuid;


class Seller extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'user_id'
    ];

    public function feedbacks(): HasMany
    {
        return $this->hasMany(SellerFeedback::class, 'seller_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gamePurchases(): HasMany
    {
        return $this->hasMany(GamePurchase::class, 'seller_id', 'id');
    }

    public function softwarePurchases(): HasMany
    {
        return $this->hasMany(SoftwarePurchase::class, 'seller_id', 'id');
    }

    public function games(): HasMany
    {
        return $this->hasMany(GameKey::class, 'seller_id', 'id');
    }

    public function software(): HasMany
    {
        return $this->hasMany(SoftwareKey::class, 'seller_id', 'id');
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'writer_id', 'user_id');
    }
}
