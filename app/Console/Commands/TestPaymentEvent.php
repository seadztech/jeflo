<?php

namespace App\Console\Commands;

use App\Events\MpesaTransactionReceived;
use App\Livewire\Transactions\Transactions;
use App\Models\Transactions as ModelsTransactions;
use Illuminate\Console\Command;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Log as FacadesLog;

class TestPaymentEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-payment-event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
   public function handle()
{
    $transaction = new ModelsTransactions();
    
    event(new MpesaTransactionReceived($transaction));
    FacadesLog::info('Test event dispatched');
}
}
