<?php

namespace App\Models;

use App\Models\News\NewsComment;
use App\Models\Purchase\GamePurchase;
use App\Models\Purchase\SoftwarePurchase;
use App\Models\Seller\Seller;
use App\Models\Seller\SellerFeedback;
use App\Models\User\Ip\UserIp;
use App\Models\User\PersonalArea\Finance\UserBalance;
use App\Models\User\Verify\UserVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Concerns\UseUuid as Uuid;
use OpenApi\Annotations as OA;
use Spatie\Permission\Traits\HasRoles;

/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="User"
 *     )
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Uuid;

    /**
     * @OA\Property(property="id", type="string", format="uuid", example="0e2090f0-d1da-4e55-ad25-0af0d2e85036"),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="surname", type="string"),
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="nickname", type="string"),
     * @OA\Property(property="avatar", type="string", format="url", example="https://example.com/avatar.jpg"),
     * @OA\Property(property="balance", ref="#/components/schemas/UserBalance"),
     */

    protected $fillable = [
        'name',
        'surname',
        'email',
        'nickname',
        'avatar',
        'password',
        'code'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function userTokens(): HasMany
    {
        return $this->hasMany(PersonalAccessToken::class, 'tokenable_id');
    }


    public function userIp(): HasMany
    {
        return $this->hasMany(UserIp::class, 'user_id');
    }


    public function userVerify(): HasMany
    {
        return $this->hasMany(UserVerifyEmail::class, 'user_email');
    }


    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class, 'user_id');
    }


    public function gamePurchases(): HasMany
    {
        return $this->hasMany(GamePurchase::class, 'user_id', 'id');
    }


    public function softwarePurchases(): HasMany
    {
        return $this->hasMany(SoftwarePurchase::class, 'user_id', 'id');
    }


    public function feedbacks(): HasMany
    {
        return $this->hasMany(SellerFeedback::class, 'user_id', 'id');
    }


    public function balance(): HasOne
    {
        return $this->HasOne(UserBalance::class, 'user_id', 'id');
    }
}
