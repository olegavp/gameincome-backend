<?php

namespace App\Models\User\Verify;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\UseUuid as Uuid;

class UserVerifyEmail extends Model
{
    use HasFactory, Uuid;

    protected $table = 'users_verify_emails';

    protected $fillable = [
        'user_email',
        'code'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
