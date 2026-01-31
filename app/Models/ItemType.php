<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Items;

class ItemType extends Model
{
    //
    protected $fillable = ['name'];
    public function items(): HasMany {
        return $this->hasMany(Items::class);
    }
}
