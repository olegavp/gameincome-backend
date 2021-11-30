<?php

namespace App\Models\MainPage;

use App\Models\Concerns\UseUuid as Uuid;
use App\Models\Item\Game;
use App\Models\Item\Software;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Recommendation extends Model
{
    use HasFactory, Uuid;

    protected $table = 'main_page_recommendations';

    public function games(): HasMany
    {
        return $this->hasMany(Game::class,'id', 'item_id');
    }

    public function software(): HasMany
    {
        return $this->hasMany(Software::class,'id', 'item_id');
    }
}
