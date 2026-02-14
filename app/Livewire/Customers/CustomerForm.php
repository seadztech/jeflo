<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class CustomerForm extends Component
{
    public $customer;
    public $sales;
    public $transactions; // will be flattened from sales
    public $ledger = [];

    public $salesTotals = [];
    public $ledgerTotals = [];

    public function mount($id)
    {
        // 🔹 Load customer with sales and their transactions
        $this->customer = Customer::with(['sales.transactions'])->findOrFail($id);

        $this->sales = $this->customer->sales;

        // 🔹 Flatten all sale transactions into one collection and sort by date
        $this->transactions = $this->sales
            ->flatMap(fn($sale) => $sale->transactions)
            ->sortBy('created_at')
            ->values();

        $this->buildLedger();
        $this->calculateTotals();
    }

  protected function buildLedger()
{
    $this->ledger = [];

    $totalDebit = 0;
    $totalCredit = 0;

    foreach ($this->transactions as $tx) {
        if ($tx->type === 'credit_sale') {
            $totalDebit += $tx->amount;
        } elseif ($tx->type === 'credit_payment') {
            $totalCredit += $tx->amount;
        }
    }

    $openingBalance = $this->customer->current_balance - ($totalDebit - $totalCredit);
    $runningBalance = $openingBalance;

    // Opening Balance
    $this->ledger[] = [
        'date' => '-',
        'reference' => '-',
        'type' => 'Opening Balance',
        'debit' => 0,
        'credit' => 0,
        'balance' => $runningBalance,
        'is_opening' => true,
    ];

    foreach ($this->transactions as $tx) {

        $debit = 0;
        $credit = 0;

        if ($tx->type === 'credit_sale') {
            $runningBalance += $tx->amount;
            $debit = $tx->amount;
        } elseif ($tx->type === 'credit_payment') {
            $runningBalance -= $tx->amount;
            $credit = $tx->amount;
        }

        $this->ledger[] = [
            'date' => $tx->created_at,
            'reference' => $tx->transaction_code,
            'type' => $tx->type,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $runningBalance,
            'is_opening' => false,
        ];
    }
}


    protected function calculateTotals()
    {
        // 🔹 Sales totals
        $this->salesTotals = [
            'total_sales' => $this->sales->sum('total_amount'),
            'total_paid' => $this->sales->sum('amount_paid'),
            'total_balance' => $this->sales->sum('balance_due'),
        ];

        // 🔹 Ledger totals
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($this->ledger as $row) {
            if (!empty($row['is_opening'])) continue;
            $totalDebit += $row['debit'];
            $totalCredit += $row['credit'];
        }

        $this->ledgerTotals = [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'closing_balance' => $this->ledger[count($this->ledger) - 1]['balance'] ?? 0,
        ];
    }

    public function render()
    {
        return view('livewire.customers.customer-form');
    }
}
