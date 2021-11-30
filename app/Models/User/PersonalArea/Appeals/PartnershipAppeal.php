<?php

namespace App\Models\User\PersonalArea\Appeals;

use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PartnershipAppeal extends Model
{
    use HasFactory, UseUuid;

    protected $table = 'partnership_appeals';

    public function messages(): HasMany
    {
        return $this->hasMany(PartnershipAppealMessage::class, 'appeal_id', 'id');
    }
}
