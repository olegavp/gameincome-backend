<?php

namespace App\Models\User\Ip;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\UseUuid as Uuid;

class UserIp extends Model
{
    use HasFactory, Uuid;

    protected $table = 'users_ip';

    protected $fillable = [
        'confirmed',
        'confirmed_at',
        'token_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
