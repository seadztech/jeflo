<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\StockChange;

class stockins extends Model
{
    //

    public function item(): BelongsTo {
        return $this->belongsTo(Items::class);
    }

    public function stockChanges() :HasMany {
        return $this->hasMany(StockChange::class);
    }

     public function receivedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'received_by',);
    } 
}
