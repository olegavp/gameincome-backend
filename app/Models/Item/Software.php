<?php

namespace App\Models\Item;

use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Software extends Model
{
    use HasFactory, UseUuid;

    public $timestamps = false;

    public function keys(): HasMany
    {
        return $this->hasMany(SoftwareKey::class, 'item_id', 'id');
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'item_genre', 'item_id', 'genre_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'item_category', 'item_id', 'category_id');
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'item_platform', 'item_id', 'platform_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'item_service', 'item_id', 'service_id');
    }
}
