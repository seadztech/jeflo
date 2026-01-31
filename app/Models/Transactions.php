<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\salesItem;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transactions extends Model
{
    //
    protected $fillable  = ['sale_id'];

    public function salesItem(): BelongsTo {
        return $this->belongsTo(salesItem::class);
    }

    public function sale(): BelongsTo {
        return $this->belongsTo(Sale::class);
    }

    // Use the correct foreign key name
    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class, 'transactions_id');
    }

    // Helper method to get allocated sales
    public function allocatedSales()
    {
        return $this->allocations()->with('sale')->get();
    }

    // Helper method to get total allocated amount
    public function getTotalAllocatedAttribute()
    {
        return $this->allocations()->sum('amount');
    }

    // Helper method to get remaining amount
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->amount - $this->total_allocated);
    }

    // Check if transaction is fully allocated
    public function getIsFullyAllocatedAttribute()
    {
        return $this->remaining_amount <= 0.01;
    }
}
