<?php

namespace App\Events;

use App\Models\Transactions;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


// app/Events/MpesaTransactionReceived.php
class MpesaTransactionReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
        // dd($this->transaction);
    }

    public function broadcastOn()
    {
        return new Channel('mpesa.transactions');
    }
}
