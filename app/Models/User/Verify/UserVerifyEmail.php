<?php

namespace App\Models\User\Verify;

use App\Models\User;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\UseUuid as Uuid;

/**
 * @property string $id
 * @property string $user_id
 * @property string $user_email
 * @property string $user_name
 * @property string $user_surname
 * @property string $user_avatar
 * @property string $user_password
 * @property int $code
 * @property string $hash
 * @property bool $is_verified
 *
 * @property timestamp $created_at
 * @property timestamp $updated_at
 */
class UserVerifyEmail extends Model
{
    use HasFactory, Uuid;

    protected $table = 'users_verify_emails';

    protected $fillable = [
        'user_email',
        'code',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
