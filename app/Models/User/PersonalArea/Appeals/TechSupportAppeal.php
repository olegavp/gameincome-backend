<?php

namespace App\Models\User\PersonalArea\Appeals;

use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class TechSupportAppeal extends Model
{
    use HasFactory, UseUuid;

    protected $table = 'tech_support_appeals';

    public function messages(): HasMany
    {
        return $this->hasMany(TechSupportAppealMessage::class, 'appeal_id', 'id');
    }
}
