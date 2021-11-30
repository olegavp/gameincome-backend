<?php

namespace App\Models\Item;

use App\Models\Concerns\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    use HasFactory, UseUuid;

    public $timestamps = false;

    public function games()
    {
        return $this->belongsToMany(Game::class, 'item_category', 'category_id', 'item_id');
    }

    public function software()
    {
        return $this->belongsToMany(Software::class, 'item_category', 'category_id', 'item_id');
    }

    public function skins()
    {
        return $this->belongsToMany(Skin::class, 'item_category', 'category_id', 'item_id');
    }

    public function cases()
    {
        return $this->belongsToMany(Cases::class, 'item_category', 'category_id', 'item_id');
    }

}
