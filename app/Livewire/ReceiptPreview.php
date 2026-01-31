<?php
namespace App\Http\Livewire;

use App\Livewire\Transactions\Transactions;
use App\Models\Transaction;
use App\Models\Transactions as ModelsTransactions;
use Livewire\Component;

class ReceiptPreview extends Component
{
    public $transactionId;
    public $transaction;

    public function mount($transactionId)
    {
        $this->transaction = ModelsTransactions::with('salesItems.item')->findOrFail($transactionId);
    }

    public function print()
    {
        $this->dispatchBrowserEvent('print-receipt');
    }

    public function render()
    {
        return view('livewire.receipt-preview');
    }
}
