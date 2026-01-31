<?php

namespace App\Livewire\Sales;

use App\Models\Allocation;
use App\Models\Items;
use App\Models\Mpesa;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\StockChange;
use App\Models\stockins;
use App\Models\Transaction;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Utils;
use App\Traits\AlertTrait;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

// Define Enums at the file level (NOT inside the class)
enum TransactionType: string
{
    case CASH = 'cash';
    case MPESA = 'mpesa';
    case CREDIT_SALE = 'credit_sale';
    case CREDIT_PAYMENT = 'credit_payment';
    case CARD = 'card';
    case BANK_TRANSFER = 'bank_transfer';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::MPESA => 'M-PESA',
            self::CREDIT_SALE => 'Credit Sale',
            self::CREDIT_PAYMENT => 'Credit Payment',
            self::CARD => 'Card',
            self::BANK_TRANSFER => 'Bank Transfer',
        };
    }

    public function isPayment(): bool
    {
        return in_array($this, [
            self::CASH,
            self::MPESA,
            self::CREDIT_PAYMENT,
            self::CARD,
            self::BANK_TRANSFER,
        ]);
    }

    public function isCredit(): bool
    {
        return in_array($this, [
            self::CREDIT_SALE,
            self::CREDIT_PAYMENT,
        ]);
    }
}

enum PaymentMethod: string
{
    case CASH = 'cash';
    case MPESA = 'mpesa';
    case CREDIT = 'credit';
    case CARD = 'card';
    case BANK_TRANSFER = 'bank_transfer';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::MPESA => 'M-PESA',
            self::CREDIT => 'Credit',
            self::CARD => 'Card',
            self::BANK_TRANSFER => 'Bank Transfer',
        };
    }

    public function requiresImmediatePayment(): bool
    {
        return $this !== self::CREDIT;
    }
}

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PARTIAL => 'Partial',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::PARTIAL => 'blue',
            self::PAID => 'green',
            self::OVERDUE => 'red',
        };
    }
}

class SalesForm extends Component
{
    use AlertTrait;

    public $saleId;
    public $saleItems = [];
    public $transactions = [];
    public $transaction_id;
    public $transaction;
    public $selectedTransactions = [];
    public $sale;

    // Payment properties
    public $paymentMethod = 'cash';
    public $isMpesa = true;
    public $mpesaPhone;
    public $cashAmount;
    public $amountReceived = 0;
    public $balance = 0;

    // Credit properties
    public $availableCredit = 0;
    public $creditDays = 30;
    public $showCreditPaymentForm = false;
    public $creditPaymentAmount = 0;
    public $customerBalance = 0;

    // Customer properties
    public $showCustomerForm = false;
    public $customerName = '';
    public $customerPhone = '';
    public $customerEmail = '';
    public $creditLimit = 0;
    public $canBuyOnCredit = false;
    public $customerSearch = '';
    public $filteredCustomers = [];
    public $editingCustomerId = null;
    public $isEditingCustomer = false;

    // Sale totals
    public $totalItems = 0;
    public $totalAmount = 0;
    public $discount = 0;
    public $finalAmount = 0;

    public $showReceipt = false;
    public $saleReceipt;

    protected $rules = [
        'mpesaPhone' => 'nullable|regex:/^07\d{8}$/',
        'cashAmount' => 'nullable|numeric|min:1',
        'customerName' => 'required|string|min:2',
        'customerPhone' => 'required|string|min:10|max:12',
        'customerEmail' => 'nullable|email',
        'creditLimit' => 'nullable|numeric|min:0',
        'creditPaymentAmount' => 'nullable|numeric|min:0',
    ];

    public function mount($id)
    {
        $this->saleId = $id;



        if ($id) {
            $sale = Sale::with(['saleItems.item', 'customer'])->find($id);
            $this->saleItems = $sale->saleItems;
            $this->sale = $sale;

           

            if (!$sale->customer) {
                $this->setWalkInCustomer();
            } else {
                $this->calculateCustomerBalance();
            }
        }

        //  dd($sale);

        $this->transactions = Transactions::orderBy('created_at', 'desc')->whereNull('sale_id')->where('type', '!=', 'credit_payment')->get()->toArray();

        $this->calculateTotals();
        $this->calculateAmountReceived();

        if ($this->sale && $this->sale->customer) {
            $this->calculateAvailableCredit();
        }
    }

    public function calculateCustomerBalance()
    {
        if ($this->sale && $this->sale->customer) {
            $this->customerBalance = $this->sale->customer->current_balance;
        }
    }

    public function toggleCreditPaymentForm()
    {
        $this->showCreditPaymentForm = !$this->showCreditPaymentForm;
        if ($this->showCreditPaymentForm && $this->sale->customer) {
            $this->creditPaymentAmount = min($this->sale->customer->current_balance, $this->sale->customer->current_balance);
            $this->calculateCustomerBalance();
        }
    }

    public function recordCreditPayment()
    {
        $this->validate([
            'creditPaymentAmount' => 'required|numeric|min:1|max:' . $this->customerBalance,
        ]);

        if (!$this->sale || !$this->sale->customer) {
            $this->showAlert('error', 'Error', 'No customer selected');
            return;
        }

        if ($this->creditPaymentAmount > $this->customerBalance) {
            $this->showAlert('error', 'Error', 'Payment amount exceeds customer balance');
            return;
        }

        DB::beginTransaction();
        try {
            $customer = $this->sale->customer;

            $transaction = new Transactions();
            $transaction->type = TransactionType::CREDIT_PAYMENT->value;
            $transaction->transaction_code = 'CREDIT-PAY-' . strtoupper(uniqid());
            $transaction->response = json_encode([
                'type' => 'credit_payment',
                'customer_id' => $customer->id,
                'previous_balance' => $customer->current_balance,
                'new_balance' => $customer->current_balance - $this->creditPaymentAmount
            ]);
            $transaction->amount = $this->creditPaymentAmount;
            $transaction->sale_id = null;
            $transaction->save();

            $customer->current_balance -= $this->creditPaymentAmount;
            $customer->save();

            $this->sale = $this->sale->fresh(['customer']);
            $this->calculateCustomerBalance();
            $this->calculateAvailableCredit();

            DB::commit();

            $this->showAlert(
                'success',
                'Credit Payment Recorded',
                'Ksh ' . number_format($this->creditPaymentAmount, 2) .
                    ' credited to customer account. New balance: Ksh ' . number_format($customer->current_balance, 2)
            );

            $this->creditPaymentAmount = 0;
            $this->showCreditPaymentForm = false;

            $this->selectedTransactions[] = $transaction->id;
            $this->calculateAmountReceived();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showAlert('error', 'Error', 'Failed to record credit payment: ' . $e->getMessage());
        }
    }

    public function clearFullCredit()
    {
        if (!$this->sale || !$this->sale->customer) {
            $this->showAlert('error', 'Error', 'No customer selected');
            return;
        }

        $this->creditPaymentAmount = $this->customerBalance;
        $this->recordCreditPayment();
    }

    public function updatedCustomerSearch($value)
    {
        if (strlen($value) > 2) {
            $this->filteredCustomers = Customer::where('name', 'like', '%' . $value . '%')
                ->orWhere('phone_number', 'like', '%' . $value . '%')
                ->orWhere('email', 'like', '%' . $value . '%')
                ->where('phone_number', '!=', 'WALK-IN')
                ->limit(10)
                ->get();
        } else {
            $this->filteredCustomers = [];
        }
    }

    public function selectCustomer($customerId)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($customerId);

            $sale = Sale::find($this->saleId);
            $sale->customer_id = $customer->id;
            $sale->save();
            $this->sale = $sale->fresh(['customer']);

            DB::commit();

            $this->showAlert('success', 'Customer Selected', 'Customer selected successfully!');
            $this->showCustomerForm = false;
            $this->customerSearch = '';

            $this->calculateCustomerBalance();
            $this->calculateAvailableCredit();

            if ($this->paymentMethod === 'credit') {
                $this->calculateAvailableCredit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showAlert('error', 'Error', 'Failed to select customer: ' . $e->getMessage());
        }
    }

    public function editCustomer()
    {
        if (!$this->sale->customer || str_starts_with($this->sale->customer->phone_number, 'WALK-IN')) {
            $this->showAlert('error', 'Cannot Edit', 'Cannot edit walk-in customer');
            return;
        }

        $customer = $this->sale->customer;
        $this->editingCustomerId = $customer->id;
        $this->isEditingCustomer = true;
        $this->customerName = $customer->name;
        $this->customerPhone = $customer->phone_number;
        $this->customerEmail = $customer->email;
        $this->creditLimit = $customer->credit_limit;
        $this->canBuyOnCredit = $customer->can_buy_on_credit;
        $this->showCustomerForm = true;
    }

    public function setWalkInCustomer()
    {
        DB::beginTransaction();
        try {
            $walkInCustomer = Customer::where('phone_number', 'WALK-IN')->first();

            if (!$walkInCustomer) {
                $walkInCustomer = new Customer();
                $walkInCustomer->name = 'Walk-in Customer';
                $walkInPhone = 'WALK-IN-' . strtoupper(uniqid());
                $walkInCustomer->phone_number = $walkInPhone;
                $walkInCustomer->credit_limit = 0;
                $walkInCustomer->current_balance = 0;
                $walkInCustomer->can_buy_on_credit = false;
                $walkInCustomer->credit_days = 0;
                $walkInCustomer->save();
            }

            $sale = Sale::find($this->saleId);
            $sale->customer_id = $walkInCustomer->id;
            $sale->save();
            $this->sale = $sale->fresh(['customer']);

            DB::commit();

            $this->showAlert('success', 'Customer Set', 'Walk-in customer selected!');
            $this->showCustomerForm = false;

            $this->calculateCustomerBalance();

            if ($this->paymentMethod === 'credit') {
                $this->addError('paymentError', 'Walk-in customers cannot use credit. Please select a different payment method.');
                $this->paymentMethod = 'cash';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showAlert('error', 'Error', 'Failed to set walk-in customer: ' . $e->getMessage());
        }
    }

    public function calculateAvailableCredit()
    {
        if (!$this->sale->customer) return;

        $customer = $this->sale->customer;
        $this->availableCredit = $customer->credit_limit - $customer->current_balance;
    }

    public function toggleCustomerForm()
    {
        $this->showCustomerForm = !$this->showCustomerForm;
        $this->resetCustomerForm();
        $this->customerSearch = '';
        $this->filteredCustomers = [];
        $this->isEditingCustomer = false;
        $this->editingCustomerId = null;
    }

    public function resetCustomerForm()
    {
        $this->customerName = '';
        $this->customerPhone = '';
        $this->customerEmail = '';
        $this->creditLimit = 0;
        $this->canBuyOnCredit = false;
    }

    public function saveCustomer()
    {
        if ($this->isEditingCustomer) {
            $this->updateCustomer();
        } else {
            $this->createCustomer();
        }
    }

    public function createCustomer()
    {
        $this->validate([
            'customerName' => 'required|string|min:2',
            'customerPhone' => 'required|string|min:10|max:12|unique:customers,phone_number',
            'customerEmail' => 'nullable|email|unique:customers,email',
            'creditLimit' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $customer = new Customer();
            $customer->name = $this->customerName;
            $customer->phone_number = $this->customerPhone;
            $customer->email = $this->customerEmail;
            $customer->credit_limit = $this->creditLimit ?: 0;
            $customer->current_balance = 0;
            $customer->can_buy_on_credit = $this->canBuyOnCredit;
            $customer->credit_days = 30;
            $customer->save();

            $sale = Sale::find($this->saleId);
            $sale->customer_id = $customer->id;
            $sale->save();
            $this->sale = $sale->fresh(['customer']);

            DB::commit();

            $this->showAlert('success', 'Customer Created', 'Customer created successfully!');
            $this->toggleCustomerForm();

            $this->calculateCustomerBalance();

            if ($this->paymentMethod === 'credit') {
                $this->calculateAvailableCredit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showAlert('error', 'Error', 'Failed to create customer: ' . $e->getMessage());
        }
    }

    public function updateCustomer()
    {
        if (!$this->editingCustomerId) {
            $this->showAlert('error', 'Error', 'No customer selected for editing');
            return;
        }

        $customer = Customer::find($this->editingCustomerId);

        $this->validate([
            'customerName' => 'required|string|min:2',
            'customerPhone' => 'required|string|min:10|max:12|unique:customers,phone_number,' . $customer->id,
            'customerEmail' => 'nullable|email|unique:customers,email,' . $customer->id,
            'creditLimit' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $customer->name = $this->customerName;
            $customer->phone_number = $this->customerPhone;
            $customer->email = $this->customerEmail;
            $customer->credit_limit = $this->creditLimit ?: 0;
            $customer->can_buy_on_credit = $this->canBuyOnCredit;
            $customer->save();

            $this->sale = $this->sale->fresh(['customer']);

            DB::commit();

            $this->showAlert('success', 'Customer Updated', 'Customer updated successfully!');
            $this->toggleCustomerForm();

            $this->calculateCustomerBalance();

            if ($this->paymentMethod === 'credit') {
                $this->calculateAvailableCredit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->showAlert('error', 'Error', 'Failed to update customer: ' . $e->getMessage());
        }
    }

    public function deleteTransaction($trx_id)
    {
        $this->transaction_id = $trx_id;
        $transaction = Transactions::find($trx_id);

        if (!$transaction) {
            $this->showAlert('error', 'Not Found', 'Transaction not found.');
            return;
        }

        if ($transaction->type === TransactionType::CASH->value || $transaction->type === TransactionType::CREDIT_PAYMENT->value) {
            $this->transaction = $transaction;

            $formattedDate = Carbon::parse($transaction->created_at)->format('d F Y h:i A');

            LivewireAlert::title('Delete Payment')
                ->text("Are you sure you want to delete payment of amount Ksh {$transaction->amount} Transaction # {$transaction->transaction_code} Date: {$formattedDate} ?")
                ->asConfirm()
                ->onConfirm('commitDeleteTransaction')
                ->show();
        } else {
            $this->showAlert('error', 'Error', 'You can only delete cash or credit payment transactions!');
            return;
        }
    }

    public function commitDeleteTransaction()
    {
        $transaction = $this->transaction;

        if (!$transaction || !in_array($transaction->type, [TransactionType::CASH->value, TransactionType::CREDIT_PAYMENT->value])) {
            $this->showAlert('error', 'Error', 'Invalid transaction type!');
            return;
        }

        // If it's a credit payment, restore the customer balance
        if ($transaction->type === TransactionType::CREDIT_PAYMENT->value && $this->sale->customer) {
            $customer = $this->sale->customer;
            $customer->current_balance += $transaction->amount;
            $customer->save();
            $this->sale = $this->sale->fresh(['customer']);
            $this->calculateCustomerBalance();
            $this->calculateAvailableCredit();
        }

        $amount = $transaction->amount;
        $transaction->delete();

        $this->calculateAmountReceived();
        $this->calculateTotals();

        $action = 'Payment Deletion';
        $description = "Successfully deleted payment of Ksh {$amount}";

        User::saveAuditTrail($action, $description);
        $this->showAlert('success', $action, $description);

        return $this->redirect(route('sale.show', $this->saleId), navigate: true);
    }

    public function toggleTransaction($transactionId)
    {
        if (in_array($transactionId, $this->selectedTransactions)) {
            $this->selectedTransactions = array_diff($this->selectedTransactions, [$transactionId]);
        } else {
            $this->selectedTransactions[] = $transactionId;
        }

        $this->calculateAmountReceived();
    }

    public function calculateAmountReceived()
    {
        $mpesaTransactions = Transactions::where('sale_id', $this->saleId)
            ->where('type', TransactionType::MPESA->value)
            ->sum('amount');

        $cashTransactions = Transactions::where('sale_id', $this->saleId)
            ->where('type', TransactionType::CASH->value)
            ->sum('amount');

        $creditPayments = Transactions::where('sale_id', $this->saleId)
            ->where('type', TransactionType::CREDIT_PAYMENT->value)
            ->sum('amount');

        $receivedAmount = Transactions::whereIn('id', $this->selectedTransactions)->sum('amount');

        $this->amountReceived = $mpesaTransactions + $cashTransactions + $creditPayments + $receivedAmount;
        $this->balance = $this->finalAmount - $this->amountReceived;
    }

    public function calculateTotals()
    {
        $sale = Sale::with('saleItems')->find($this->saleId);

        $this->totalItems = $sale->saleItems->count();
        $this->totalAmount = $sale->saleItems->sum(fn($item) => $item->unit_price * $item->quantity);
        $this->finalAmount = $this->totalAmount - $this->discount;
    }

    public function setPaymentMethod($method)
    {
        $this->paymentMethod = $method;

        $this->cashAmount = null;
        $this->mpesaPhone = null;

        $this->isMpesa = $method === 'mpesa';

        if ($method === 'credit') {
            $this->checkCreditEligibility();
        }
    }

    public function setMpesaForm()
    {
        $this->isMpesa = !$this->isMpesa;
        $this->paymentMethod = $this->isMpesa ? 'mpesa' : 'cash';
    }

    public function checkCreditEligibility()
    {
        if (!$this->sale || !$this->sale->customer) {
            $this->addError('paymentError', 'Please select a customer for credit sale');
            return false;
        }

        $customer = $this->sale->customer;

        if (str_starts_with($customer->phone_number, 'WALK-IN')) {
            $this->addError('paymentError', 'Walk-in customers cannot use credit. Please select a registered customer.');
            return false;
        }

        if (!$customer->can_buy_on_credit) {
            $this->addError('paymentError', 'This customer is not approved for credit purchases');
            return false;
        }

        $this->calculateAvailableCredit();

        if ($this->availableCredit < $this->totalAmount) {
            $this->addError(
                'paymentError',
                "Insufficient credit. Available: Ksh " . number_format($this->availableCredit, 2) .
                    ", Required: Ksh " . number_format($this->totalAmount, 2)
            );
            return false;
        }

        return true;
    }

    public function recordCashPayment()
    {
        if ($this->cashAmount < 1) {
            $this->addError('error', 'Cash amount must be at least 1.');
            return;
        }

        $transaction = new Transactions();
        $transaction->type = TransactionType::CASH->value;
        $transaction->transaction_code = 'CASH-' . strtoupper(uniqid());
        $transaction->response = json_encode(['type' => 'cash_payment']);
        $transaction->amount = $this->cashAmount;
        $transaction->sale_id = null;
        $transaction->save();

        $this->calculateAmountReceived();
        $this->calculateTotals();
        $this->cashAmount = null;

        $action = 'Cash Payment Record';
        $description = "Successfully created payment of Ksh {$transaction->amount}";

        User::saveAuditTrail($action, $description);

        return $this->redirect(route('sale.show', $this->saleId), navigate: true);
    }

    public function sendSTK()
    {
        $this->validate([
            'mpesaPhone' => 'required|string',
        ]);

        $mpesaClient = new Mpesa();
        $mpesaClient->sendSTKPush($this->mpesaPhone, $this->totalAmount, 'Sale Payment');
    }

    public function confirmPayment()
    {
        if ($this->paymentMethod === 'credit') {
            return $this->processCreditSale();
        }

        return $this->processCashOrMpesaSale();
    }

    private function processCreditSale()
    {
        if (!$this->checkCreditEligibility()) {
            return;
        }

        $sale = Sale::with(['saleItems', 'customer'])->findOrFail($this->saleId);
        $customer = $sale->customer;

        if ($sale->status == 'completed') {
            $this->showAlert('error', 'Sale Error', 'This sale has already been completed!');
            return $this->redirect(route('pos'), navigate: true);
        }

        DB::beginTransaction();

        try {
            $outOfStockItems = [];

            foreach ($sale->saleItems as $item) {
                $itemId = $item->item_id;
                $requiredQty = $item->quantity;

                $stockins = stockins::where('item_id', $itemId)
                    ->where('quantity', '>', 0)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->get();

                $remainingQty = $requiredQty;
                $totalProcessed = 0;

                foreach ($stockins as $stockin) {
                    if ($remainingQty <= 0) {
                        break;
                    }

                    $availableQty = $stockin->quantity;
                    $deductQty = min($remainingQty, $availableQty);

                    $stockin->quantity -= $deductQty;
                    $stockin->save();

                    $remainingQty -= $deductQty;
                    $totalProcessed += $deductQty;

                    $change = new StockChange();
                    $change->stockins_id = $stockin->id;
                    $change->quantity = $deductQty;
                    $change->actionBy = Auth::user()->id;
                    $change->changeType = 'decrement';
                    $change->reason = 'sale';
                    $change->save();
                }

                if ($totalProcessed < $requiredQty) {
                    $product = Items::find($itemId);
                    $outOfStockItems[] = $product ? $product->name : "Item ID: $itemId";
                }
            }

            if (!empty($outOfStockItems)) {
                DB::rollBack();
                $this->showAlert('error', 'Stock Error', 'Insufficient stock for: ' . implode(', ', $outOfStockItems));
                return;
            }

            $sale->total_amount = $this->totalAmount;
            $sale->payment_method = 'credit';
            $sale->payment_status = 'pending';
            $sale->amount_paid = 0;
            $sale->balance_due = $this->totalAmount;
            $sale->due_date = now()->addDays($this->creditDays);
            $sale->status = 'completed';
            $sale->save();

            $customer->current_balance += $this->totalAmount;
            $customer->save();

            $transaction = new Transactions();
            $transaction->type = TransactionType::CREDIT_SALE->value;
            $transaction->transaction_code = 'CREDIT-' . strtoupper(uniqid());
            $transaction->response = json_encode([
                'type' => 'credit_sale',
                'due_date' => $sale->due_date,
                'customer_id' => $customer->id,
                'previous_balance' => $customer->current_balance - $this->totalAmount,
                'new_balance' => $customer->current_balance,
                'sale_id' => $sale->id,
            ]);

            $transaction->amount = $this->totalAmount;
            $transaction->sale_id = $sale->id;
            $transaction->save();

            Allocation::create([
                'transactions_id' => $transaction->id,
                'sale_id' => $sale->id,
                'amount' => $transaction->amount,
                'notes' => 'This allocation was done automatically during sale completion',
                'allocated_by' => Auth::id(),
                'allocated_at' => now(),
            ]);

            DB::commit();

            $action = 'Credit Sale Completion';
            $description = 'Successfully completed credit sale. Due date: ' . $sale->due_date->format('Y-m-d');
            User::saveAuditTrail($action, $description);

            $this->dispatch('sale-completed', message: 'Credit sale recorded successfully!');

            $this->openReceipt();

            return $this->redirect(route('pos'), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('paymentError', 'Failed to complete credit sale: ' . $e->getMessage());
        }
    }

    private function processCashOrMpesaSale($paymentMethod = null)
    {
        $sale = Sale::with('saleItems')->findOrFail($this->saleId);

        if ($sale->status == 'completed') {
            $this->showAlert('error', 'Sale Error', 'This sale has already been sold out!');
            return $this->redirect(route('pos'), navigate: true);
        }

        if ($this->amountReceived < $this->finalAmount) {
            $this->showAlert('error', 'Payment Error', 'Amount received is less than the total payable.');
            return;
        }

        DB::beginTransaction();

        try {
            $outOfStockItems = [];

            foreach ($sale->saleItems as $item) {
                $itemId = $item->item_id;
                $requiredQty = $item->quantity;

                $stockins = stockins::where('item_id', $itemId)
                    ->where('quantity', '>', 0)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->get();

                $remainingQty = $requiredQty;
                $totalProcessed = 0;

                foreach ($stockins as $stockin) {
                    if ($remainingQty <= 0) {
                        break;
                    }

                    $availableQty = $stockin->quantity;
                    $deductQty = min($remainingQty, $availableQty);

                    $stockin->quantity -= $deductQty;
                    $stockin->save();

                    $remainingQty -= $deductQty;
                    $totalProcessed += $deductQty;

                    $change = new StockChange();
                    $change->stockins_id = $stockin->id;
                    $change->quantity = $deductQty;
                    $change->actionBy = Auth::user()->id;
                    $change->changeType = 'decrement';
                    $change->reason = 'sale';
                    $change->save();
                }

                if ($totalProcessed < $requiredQty) {
                    $product = Items::find($itemId);
                    $outOfStockItems[] = $product ? $product->name : "Item ID: $itemId";
                }
            }

            if (!empty($outOfStockItems)) {
                DB::rollBack();
                $this->showAlert('error', 'Stock Error', 'Insufficient stock for: ' . implode(', ', $outOfStockItems));
                return;
            }

            $sale->total_amount = $this->totalAmount;
            $sale->payment_method = $paymentMethod ? $paymentMethod : $this->paymentMethod;
            $sale->payment_status = 'paid';
            $sale->amount_paid = $this->amountReceived;
            $sale->balance_due = 0;
            $sale->paid_at = now();
            $sale->status = 'completed';
            $sale->served_by = Auth::user()->id;
            $sale->save();

            $transactions = Transactions::whereIn('id', $this->selectedTransactions)->get();

            foreach ($transactions as $transaction) {
                $transaction->update([
                    'sale_id' => $sale->id,
                ]);

                Allocation::create([
                    'transactions_id' => $transaction->id,
                    'sale_id' => $sale->id,
                    'amount' => $transaction->amount,
                    'notes' => 'This allocation was done automatically during sale completion',
                    'allocated_by' => Auth::id(),
                    'allocated_at' => now(),
                ]);
            }

            DB::commit();

            $action = 'Sale Completion';
            $description = 'Successfully completed sale with DB ID: ' . $sale->id;
            User::saveAuditTrail($action, $description);

            $this->dispatch('sale-completed', message: 'Sale recorded successfully!');

            $this->openReceipt();

            return $this->redirect(route('pos'), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('paymentError', 'Failed to complete sale: ' . $e->getMessage());
            $this->showAlert('error', $e->getMessage(), 'Error');
        }
    }

    #[On('echo:mpesa.transactions,MpesaTransactionReceived')]
    public function handleTransaction($payload)
    {
        $data = $payload['transaction'];

        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $Amount = $data['Amount'] ?? null;
        $TransactionCode = $data['TransactionCode'] ?? null;
        $ResultCode = $data['ResultCode'] ?? null;
        $ResultDesc = $data['ResultDesc'] ?? null;
        $transactionID = $data['transactionID'] ?? null;

        if ($ResultCode == 0) {
            $formattedDate = Utils::formatDate($data['TransactionDate']);

            $transaction = Transactions::find($transactionID);
            if ($transaction) {
                $transaction->type = TransactionType::MPESA->value;
                $transaction->sale_id = $this->saleId;
                $transaction->save();
            }

            $this->processCashOrMpesaSale();

            $this->showAlert(
                'success',
                'PAYMENT CONFIRMATION',
                "$TransactionCode of Ksh $Amount has been received on $formattedDate"
            );

            $this->dispatch('sale-completed', message: 'Sale recorded successfully!');
        } else {
            $this->showAlert('error', 'PAYMENT NOT RECEIVED', $ResultDesc);
        }

        return $this->redirect(route('sale.show', $this->saleId), navigate: true);
    }

    public function openReceipt()
    {
        $url = route('sales.receipt.pdf', $this->saleId);
        $this->js(
            <<<JS
                const width = 800;
                const height = 600;
                const top = 100;
                const left = Math.max(0, (window.screen.width - width) / 2);

                window.open("{$url}", "_blank", `width=\${width},height=\${height},top=\${top},left=\${left},scrollbars=yes,resizable=yes`);
            JS
        );
    }

    public function viewReceipt()
    {
        $this->openReceipt();
    }

    public function render()
    {
        return view('livewire.sales.sales-form');
    }
}
