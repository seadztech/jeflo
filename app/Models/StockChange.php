<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\stockins;

class StockChange extends Model
{
    //


    public function stockin(){
        return $this->belongsTo(stockins::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'actionBy', 'id');
    }

    
}
