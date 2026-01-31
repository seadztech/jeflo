<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Items;
use App\Models\ItemType;
use App\Models\Sale;
use App\Models\salesItem;
use App\Models\Transactions;
use App\Traits\AlertTrait;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ShopPos extends Component
{
    use AlertTrait;

    // POS State values
    public $search = '';
    public $selectedCategory = null;
    public $item_id = null;

    // Customer
    public $customerSearch = '';
    public $selectedCustomer = null;

    // Cart
    public $cartItems = [];
    public $totalAmount = 0;
    public $totalItems = 0;
    public $discount = 0;
    public $cashAmount;

    // Payment
    public $showPaymentPage = false;
    public $paymentMethod = 'cash';
    public $amountTendered = 0;
    public $mpesaNumber = '';
    public $splitPayment = false;
    public $splitMethod1 = 'cash';
    public $splitMethod2 = 'mpesa';
    public $splitAmount1 = 0;
    public $splitAmount2 = 0;
    public $transactionType;

    // Transactions
    public $transactionCode;
    public $holdTransactions = [];
    public $transactions = [];
    public $selectedHoldTransaction = null;
    public $selectedTransactions = [];

    // UI State
    public $showReceipt = false;
    public $receiptData = [];
    public $isMpesa = false;
    public $isPaymentForm = false;

    // Data
    public $popularItems = [];
    public $categories = [];

    protected $listeners = [
        'itemAdded' => '$refresh',
        'customerSelected' => 'selectCustomer',
    ];

    public function mount()
    {
        $this->initializePos();
        $this->transactions = Transactions::orderBy('created_at', 'desc')->get();
    }

    protected function initializePos()
    {
        $this->loadPopularItems();
        $this->loadCategories();
        $this->generateTransactionCode();
        $this->loadHoldTransactions();
    }

    public function toggleTransaction($trxId)
    {
        if (in_array($trxId, $this->selectedTransactions)) {
            $this->selectedTransactions = array_diff($this->selectedTransactions, [$trxId]);
        } else {
            $this->selectedTransactions[] = $trxId;
        }
    }

    public function sendSTK() {}

    public function querySTK()
    {
        // Implement logic to query STK status
    }

    protected function generateTransactionCode()
    {
        $this->transactionCode = 'TRX-' . date('Ymd') . '-' . Str::upper(Str::random(6));
    }

    // public function checkOut($sale_id){
    //     dd($sale_id);

    // }

    public function makeSale()
    {
        if (empty($this->cartItems)) {
            $this->showAlert('error', 'Cannot process empty cart', 'error');
            return;
        }

        $user  = User::where('id', Auth::user()->id)->whereNotNull('branch_id')->first();

        if(!$user){
            $this->showAlert('error', "The currently logged in user does'nt have a branch assigned", 'error');
             return;
        } 


        DB::beginTransaction();

        $outOfStockItems = [];
        // dd($this->customer_id ?? 1);
        try {
            // 1. Create one Sale record at the beginning
            $sale = new Sale();
            $sale->item_id = 
            $sale->customer_id = $this->customer_id ?? 1;
            $sale->total_amount = 0; 
            $sale->actionBy = Auth::user()->id;
            $sale->status = 'pending';
            $sale->branch_id = $user->branch->id;
            $sale->save();


            foreach ($this->cartItems as $cartItem) {
                $requiredQty = $cartItem['quantity'];
                $itemId = $cartItem['id'];

                // Create salesItem linked to the one Sale
                $saleItem = new salesItem();
                $saleItem->sale_id = $sale->id;
                $saleItem->stockin_id = 0;
                $saleItem->item_id = $itemId;
                $saleItem->quantity = $requiredQty;
                $saleItem->unit_price = $cartItem['price'];
                $saleItem->total_price = $requiredQty * $cartItem['price'];
                $saleItem->status = 'pending';
                $saleItem->save();
            }

            if (!empty($outOfStockItems)) {
                DB::rollBack();
                $names = implode(', ', $outOfStockItems);
                $this->showAlert('error', "Insufficient stock for the following items: [ $names ]", 'error');
                return; 
            } 

    
            DB::commit();

            // $this->showAlert('success', 'Payment processed .', 'success');
            // $this->resetCart();

            return $this->redirect(route('sale.show', $sale->id), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            $this->showAlert('error', $e->getMessage(), 'error');
        }
    }

    protected function loadPopularItems()
    {
        $this->popularItems = Items::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('item_type_id', $this->selectedCategory);
            })
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->unit_price,
                    'image' => $item->image_url ?? asset('/images/toolbox.png'),
                    'category' => $item->itemType->name ?? 'Uncategorized',
                ];
            });
    }

    protected function loadCategories()
    {
        $this->categories = ItemType::all()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'active' => $this->selectedCategory == $category->id,
            ];
        });
    }

   public function setCategory($categoryId)
{
    // Handle "All" category (null)
    if ($categoryId === 'null') {
        $this->selectedCategory = null;
    } else {
        // Toggle category selection
        $this->selectedCategory = $categoryId == $this->selectedCategory ? null : (int)$categoryId;
    }
    
    $this->loadPopularItems();
}
    public function updatedSearch()
    {
        $this->loadPopularItems();
    }

    public function updatedItemId($value)
    {
        if ($value) {
            $this->addToCart($value);
            $this->item_id = null;
        }
    }

    public function addToCart($itemId)
    {
        $item = Items::findOrFail($itemId);

        if (isset($this->cartItems[$itemId])) {
            $this->cartItems[$itemId]['quantity'] += 1;
        } else {
            $this->cartItems[$itemId] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->unit_price,
                'quantity' => 1,
                'image' => $item->image_url ?? 'https://via.placeholder.com/150',
            ];
        }

        $this->updateCartTotals();
        $this->dispatch('itemAdded');
        $this->showAlert('Item Added', $item->name . ' added to cart', 'success');
    }

    public function removeFromCart($itemId)
    {
        unset($this->cartItems[$itemId]);
        $this->updateCartTotals();
        $this->dispatch('itemAdded');
    }

    public function incrementQuantity($itemId)
    {
        $this->cartItems[$itemId]['quantity'] += 1;
        $this->updateCartTotals();
        $this->dispatch('itemAdded');
    }

    public function decrementQuantity($itemId)
    {
        if ($this->cartItems[$itemId]['quantity'] > 1) {
            $this->cartItems[$itemId]['quantity'] -= 1;
            $this->updateCartTotals();
            $this->dispatch('itemAdded');
        } else {
            $this->removeFromCart($itemId);
        }
    }

    protected function updateCartTotals()
    {
        $this->totalItems = collect($this->cartItems)->sum('quantity');
        $this->totalAmount =
            collect($this->cartItems)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            }) - $this->discount;
    }

    public function updatedDiscount($value)
    {
        $this->validate([
            'discount' => 'numeric|min:0|max:' . $this->totalAmount,
        ]);

        $this->updateCartTotals();
    }

    public function holdTransaction()
    {
        if (empty($this->cartItems)) {
            $this->showAlert('Hold Transaction', 'Cannot hold empty transaction', 'error');
            return;
        }

        DB::transaction(function () {
            $transaction = Transactions::create([
                'transaction_code' => $this->transactionCode,
                'amount' => $this->totalAmount,
                'type' => 'hold',
                'customer_id' => $this->selectedCustomer,
                'response' => json_encode([
                    'discount' => $this->discount,
                ]),
            ]);

            foreach ($this->cartItems as $item) {
                salesItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);
            }
        });

        $this->resetCart();
        $this->showAlert('success', 'Transaction has been held successfully', 'success');
    }

    public function loadHoldTransactions()
    {
        $this->holdTransactions = Transactions::latest()
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'code' => $transaction->transaction_code,
                    'amount' => $transaction->amount,
                    'customer' => $transaction->customer->name ?? 'Walk-in',
                    // 'items' => $transaction->salesItems->count(),
                    'date' => $transaction->created_at->format('M d, Y h:i A'),
                ];
            });
    }

    public function loadHoldTransaction($transactionId)
    {
        $transaction = Transactions::with('salesItems.item')->findOrFail($transactionId);

        $this->cartItems = [];
        foreach ($transaction->salesItems as $item) {
            $this->cartItems[$item->item_id] = [
                'id' => $item->item_id,
                'name' => $item->item->name,
                'price' => $item->unit_price,
                'quantity' => $item->quantity,
                'image' => $item->item->image_url ?? 'https://via.placeholder.com/150',
            ];
        }

        $this->selectedCustomer = $transaction->customer_id;
        $this->customerSearch = $transaction->customer->name ?? '';
        $this->discount = json_decode($transaction->response)->discount ?? 0;
        $this->selectedHoldTransaction = $transactionId;
        $this->updateCartTotals();
    }

    public function processPayment($method)
    {
        $this->paymentMethod = $method;

        if ($method != 'cash') {
            $this->amountTendered = $this->totalAmount;
        }
    }

    // Start of payments logic

public function confirmPayment()
{
    $this->validate($this->getPaymentValidationRules());

    // HARD BLOCK underpayment
    if (!$this->splitPayment && $this->amountTendered < $this->totalAmount) {
        $this->showAlert('error', 'Payment amount is less than total amount');
        return;
    }

    try {
        DB::transaction(function () {
            $paymentData = $this->preparePaymentData();
            $this->processPaymentTransaction($paymentData);
        });

        $this->showPaymentPage = false;
        $this->showAlert('success', 'Payment processed successfully!', 'success');
    } catch (\Exception $e) {
        $this->addError('paymentError', $e->getMessage());
    }
}

protected function preparePaymentData()
{
    if ($this->splitPayment) {
        return [
            'method' => 'split',
            'amount_received' => $this->splitAmount1 + $this->splitAmount2,
            'change' => 0,
            'details' => [
                ['method' => $this->splitMethod1, 'amount' => $this->splitAmount1],
                ['method' => $this->splitMethod2, 'amount' => $this->splitAmount2],
            ],
        ];
    }

    return [
        'method' => $this->paymentMethod,
        'amount_received' => $this->amountTendered,
        'change' => $this->paymentMethod == 'cash'
            ? $this->amountTendered - $this->totalAmount
            : 0,
        'mpesa_number' => $this->paymentMethod == 'mpesa' ? $this->mpesaNumber : null,
    ];
}


    protected function processPaymentTransaction($paymentData)
    {
        $transactionData = [
            'transaction_code' => $this->transactionCode,
            'amount' => $this->totalAmount,
            'status' => 'completed',
            'type' => $paymentData['method'],
            'customer_id' => $this->selectedCustomer,
            'response' => json_encode([
                'payment_method' => $paymentData['method'],
                'amount_received' => $paymentData['amount_received'],
                'change' => $paymentData['change'] ?? 0,
                'discount' => $this->discount,
                'mpesa_number' => $paymentData['mpesa_number'] ?? null,
                'split_details' => $paymentData['details'] ?? null,
            ]),
        ];

        if ($this->selectedHoldTransaction) {
            $transaction = Transactions::find($this->selectedHoldTransaction);
            $transaction->update($transactionData);
        } else {
            $transaction = Transactions::create($transactionData);
        }

        foreach ($this->cartItems as $item) {
            salesItem::updateOrCreate(
                [
                    'transaction_id' => $transaction->id,
                    'item_id' => $item['id'],
                ],
                [
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ],
            );
        }

        $this->prepareReceipt($transaction, $paymentData);
        $this->showReceipt = true;
        $this->resetCart();
    }

   protected function getPaymentValidationRules()
{
    $rules = [
        'paymentMethod' => 'required|in:cash,mpesa,card,bank',
    ];

    if (!$this->splitPayment) {
        $rules['amountTendered'] = 'required|numeric|min:' . $this->totalAmount;
    }

    if ($this->paymentMethod == 'mpesa') {
        $rules['mpesaNumber'] = 'required|regex:/^254[0-9]{9}$/';
    }

    if ($this->splitPayment) {
        $rules = [
            'splitAmount1' => 'required|numeric|min:0.01',
            'splitAmount2' => 'required|numeric|min:0.01',
            'splitMethod1' => 'required|in:cash,mpesa,card,bank',
            'splitMethod2' => 'required|in:cash,mpesa,card,bank',
        ];

        if (($this->splitAmount1 + $this->splitAmount2) != $this->totalAmount) {
            throw new \Exception('Split payment must equal total amount.');
        }
    }

    return $rules;
}

    public function recordCashPayment()
    {
        $this->validate([
            'cashAmount' => 'required|numeric|min:0|',
        ]);
        LivewireAlert::title('Create cash payments')
            ->text("Are you sure you want to record cash of amount $this->cashAmount ?")
            ->asConfirm()
            ->onConfirm('commitRecordCashPayment')
            ->show();
    }

    public function commitRecordCashPayment()
    {
        $transaction = new Transactions();
        $transaction->type = 1;
        $transaction->amount = $this->cashAmount;
        $transaction->transaction_code = 'Cash';
        $transaction->save();

        $action = 'Cash Payment  Record';

        $description = "Successfully created $transaction->type of amount $transaction->amount";

        User::saveAuditTrail($action, $description);

        $this->showAlert('success', $action, $description);

        return $this->redirect(route('pos'), navigate: true);
        $this->skipRender();
    }

    protected function prepareReceipt($transaction, $paymentData)
    {
        $this->receiptData = [
            'transaction_code' => $transaction->transaction_code,
            'date' => $transaction->created_at->format('M d, Y h:i A'),
            'items' => collect($this->cartItems)
                ->map(function ($item) {
                    return [
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['price'] * $item['quantity'],
                    ];
                })
                ->values()
                ->toArray(),
            'subtotal' => $this->totalAmount + $this->discount,
            'discount' => $this->discount,
            'total' => $this->totalAmount,
            'payment_method' => $paymentData['method'],
            'amount_received' => $paymentData['amount_received'],
            'change' => $paymentData['change'] ?? 0,
            'customer' => $transaction->customer->name ?? 'Walk-in Customer',
            'mpesa_number' => $paymentData['mpesa_number'] ?? null,
            'split_details' => $paymentData['details'] ?? null,
        ];
    }

    public function cancelTransaction()
    {
        $this->resetCart();
        $this->showAlert('info', 'Current transaction has been cleared', 'info');
    }

    public function resetCart()
    {
        $this->cartItems = [];
        $this->totalAmount = 0;
        $this->totalItems = 0;
        $this->discount = 0;
        $this->selectedCustomer = null;
        $this->customerSearch = '';
        $this->selectedHoldTransaction = null;
        $this->generateTransactionCode();
        $this->dispatch('itemAdded');
        $this->showPaymentPage = false;
    }

    public function closeReceipt()
    {
        $this->showReceipt = false;
    }

    public function setMpesaForm()
    {
        $this->isMpesa = !$this->isMpesa;
    }

    public function showPaymentsForm()
    {
        $this->isPaymentForm = !$this->isPaymentForm;
    }
    // End of payments logic

    public function searchCustomers()
    {
        return Customer::where('name', 'like', '%' . $this->customerSearch . '%')
            ->orWhere('phone', 'like', '%' . $this->customerSearch . '%')
            ->take(5)
            ->get();
    }

    public function selectCustomer($customerId)
    {
        $this->selectedCustomer = $customerId;
        $customer = Customer::find($customerId);
        $this->customerSearch = $customer->name;
    }

    public function render()
    {
        return view('livewire.shop-pos', [
            'categories' => $this->categories,
            'popularItems' => $this->popularItems,
        ]);
    }
}
