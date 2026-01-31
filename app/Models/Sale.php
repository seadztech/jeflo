<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\salesItem;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    //
    public function saleItems(){
        return $this->hasMany(salesItem::class);
    }

    public function transactions(){
        return $this->hasMany(Transactions::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'actionBy', 'id');
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

     public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    // Helper method to get allocated amount for a specific transaction
    public function allocatedAmountForTransaction($transactionId)
    {
        return $this->allocations()
            ->where('transactions_id', $transactionId)
            ->sum('amount');
    }

    // Helper method to get total allocated amount across all transactions
    public function getTotalAllocatedAttribute()
    {
        return $this->allocations()->sum('amount');
    }

    // Helper method to get remaining unallocated amount
    public function getUnallocatedAmountAttribute()
    {
        return max(0, $this->total_amount - $this->total_allocated);
    }



}
