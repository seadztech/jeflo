<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class salesItem extends Model
{
    //
    public function item(): BelongsTo {
        return $this->belongsTo(Items::class);
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transactions::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function carts(){
        return $this->hasMany(Cart::class);
    }
    public function sale(){
        return $this->belongsTo(Sale::class);
    }

}

