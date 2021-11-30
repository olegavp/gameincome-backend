<?php

namespace App\Models\User\PersonalArea\Appeals;

use App\Models\AdminUser;
use App\Models\Concerns\UseUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;


class DisputeAppealMessage extends Model
{
    use HasFactory, UseUuid;

    protected $table = 'dispute_appeals_messages';

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function admin(): HasOne
    {
        return $this->hasOne(AdminUser::class, 'id', 'admin_id');
    }
}
