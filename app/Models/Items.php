<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Items extends Model
{
    //
    protected $fillable = ['name', 'item_type_id'];

    public function stockins(): HasMany
    {
        return $this->hasMany(stockins::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class);
    }

    public function item_type(): BelongsTo
    {
        return $this->belongsTo(ItemType::class);
    }

    public function sales_items(): BelongsTo
    {
        return $this->belongsTo(salesItem::class);
    }

    public function sale(){
        return $this->belongsTo(Sale::class);
    }

    // public function stockins(): HasMany
    // {
    //     return $this->hasMany(stockins::class);
    // }

   

}
