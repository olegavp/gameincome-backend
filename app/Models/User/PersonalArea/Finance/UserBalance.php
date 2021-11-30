<?php

namespace App\Models\User\PersonalArea\Finance;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\UseUuid as Uuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     @OA\Xml(
 *         name="UserBalance"
 *     )
 * )
 */
class UserBalance extends Model
{
    use HasFactory, Uuid;

    /**
     * @OA\Property(property="overallBalance", type="integer"),
     * @OA\Property(property="pendingBalance", type="integer"),
     * @OA\Property(property="availableBalance", type="integer"),
     * @OA\Property(property="blockingBalance", type="integer"),
     */

    protected $table = 'users_balance';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'overall',
        'pending',
        'available'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
