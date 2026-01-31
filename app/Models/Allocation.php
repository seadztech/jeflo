<?php
// app/Models/Allocation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allocation extends Model
{
    protected $fillable = [
        'transactions_id',
        'sale_id',
        'amount',
        'notes',
        'allocated_by',
        'allocated_at'
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
    ];

    // Note: using 'transactions_id' (plural) not 'transaction_id' (singular)
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transactions::class, 'transactions_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function allocatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }
}