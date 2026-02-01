<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MpesaTransactionReceived implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $transaction;

    public function __construct(array $transaction)
    {
        $this->transaction = $transaction;
    }

    public function broadcastOn()
    {
        return new Channel('mpesa.transactions');
    }

    public function broadcastAs()
    {
        return 'MpesaTransactionReceived';
    }
}
