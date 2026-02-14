<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\Transactions;
use Livewire\Component;

class CustomerForm extends Component
{
    public $customer;
    public $sales;
    public $ledger = [];

    public $salesTotals = [];
    public $ledgerTotals = [];

    public function mount($id)
    {
        // 🔹 Load customer with sales and their transactions
        $this->customer = Customer::with('sales.transactions')->findOrFail($id);

        $this->sales = $this->customer->sales;

        $this->buildLedger();
        $this->calculateTotals();
    }

    protected function buildLedger()
    {
        $this->ledger = [];

        $runningBalance = 0;

        // 🔹 Flatten all sale transactions
        $saleTransactions = $this->sales->flatMap(fn($sale) => $sale->transactions);

        // 🔹 Get standalone credit_payment transactions for this customer
        $creditPayments = Transactions::where('type', 'credit_payment')->get()
            ->filter(function($tx) {
                $response = json_decode($tx->response, true);
                return isset($response['customer_id']) && $response['customer_id'] == $this->customer->id;
            });

        // 🔹 Merge all transactions and sort by date
        $allTransactions = $saleTransactions->merge($creditPayments)
            ->sortBy('created_at')
            ->values();

        // 🔹 Calculate opening balance
        $totalDebit = $allTransactions->where('type', 'credit_sale')->sum('amount');
        $totalCredit = $allTransactions->where('type', 'credit_payment')->sum('amount');

        $openingBalance = $this->customer->current_balance - ($totalDebit - $totalCredit);
        $runningBalance = $openingBalance;

        // 🔹 Opening balance row
        $this->ledger[] = [
            'date' => '-',
            'reference' => '-',
            'type' => 'Opening Balance',
            'debit' => 0,
            'credit' => 0,
            'balance' => $runningBalance,
            'is_opening' => true,
        ];

        // 🔹 Add transactions to ledger
        foreach ($allTransactions as $tx) {
            $debit = $credit = 0;

            if ($tx->type === 'credit_sale') {
                $debit = $tx->amount;
                $runningBalance += $tx->amount;
            } elseif ($tx->type === 'credit_payment') {
                $credit = $tx->amount;
                $runningBalance -= $tx->amount;
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
            'closing_balance' => $this->ledger[count($this->ledger)-1]['balance'] ?? 0,
        ];
    }

    public function render()
    {
        return view('livewire.customers.customer-form');
    }
}
