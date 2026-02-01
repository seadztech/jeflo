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
use Illuminate\Support\Facades\Log;

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
    public $searching = false;
    public $lastTransaction = null;

    // Data
    public $popularItems = [];
    public $categories = [];
    public $debug = false; // For debugging

    protected $listeners = [
        'itemAdded' => '$refresh',
        'customerSelected' => 'selectCustomer',
        'doSearch' => 'focusSearch',
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

        // Debug: Check if we're loading items
        if ($this->debug) {
            Log::info('ShopPos initialized', [
                'popularItems_count' => count($this->popularItems),
                'categories_count' => count($this->categories),
            ]);
        }
    }

    // Add this method for manual search update
    public function updateSearch()
    {
        $this->searching = true;
        $this->loadPopularItems();
        $this->searching = false;
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

    public function makeSale()
    {
        if (empty($this->cartItems)) {
            $this->showAlert('error', 'Cannot process empty cart', 'error');
            return;
        }

        $user = User::where('id', Auth::user()->id)->whereNotNull('branch_id')->first();

        if (!$user) {
            $this->showAlert('error', "The currently logged in user doesn't have a branch assigned", 'error');
            return;
        }

        DB::beginTransaction();

        $outOfStockItems = [];

        try {
            // Create Sale record
            $sale = new Sale();
            $sale->customer_id = $this->selectedCustomer ?? 1;
            $sale->total_amount = $this->totalAmount;
            $sale->actionBy = Auth::user()->id;
            $sale->status = 'pending';
            $sale->branch_id = $user->branch_id;
            $sale->save();

            // dd($this->cartItems);

            foreach ($this->cartItems as $cartItem) {
                $requiredQty = $cartItem['quantity'];
                $itemId = $cartItem['id'];

                // Debug: Check the cart item structure
                Log::info('Cart item data', [
                    'cartItem' => $cartItem,
                    'price' => $cartItem['price'] ?? null,
                    'has_price' => isset($cartItem['price']),
                ]);



                // Create salesItem - ensure price is set
                $saleItem = new salesItem();
                $saleItem->sale_id = $sale->id;
                $saleItem->stockin_id = 0;
                $saleItem->item_id = $itemId;
                $saleItem->quantity = $requiredQty;
                $saleItem->unit_price = $cartItem['price']; // Make sure this is not null
                $saleItem->total_price = $requiredQty * $cartItem['price'];
                $saleItem->status = 'pending';
                $saleItem->save();
            }

            DB::commit();

            $this->lastTransaction = $sale->id;

            return $this->redirect(route('sale.show', $sale->id), navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sale creation failed: ' . $e->getMessage());
            $this->showAlert('error', 'Failed to process sale: ' . $e->getMessage(), 'error');
        }
    }

    protected function loadPopularItems()
    {
        try {
            if ($this->debug) {
                Log::info('Loading popular items', [
                    'search' => $this->search,
                    'selectedCategory' => $this->selectedCategory,
                ]);
            }

            $query = Items::query()
                ->when($this->selectedCategory, function ($q) {
                    return $q->where('item_type_id', $this->selectedCategory);
                })
                ->when($this->search, function ($q) {
                    $searchTerm = '%' . $this->search . '%';
                    return $q->where(function ($query) use ($searchTerm) {
                        $query->where('name', 'like', $searchTerm)
                            ->orWhere('description', 'like', $searchTerm);
                            
                            
                    });
                })
                ->orderBy('name')
                ->limit(40);

            // Get the items
            $items = $query->get();

            if ($this->debug) {
                Log::info('Items query results', [
                    'count' => $items->count(),
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings(),
                ]);
            }

            // Transform to array format
            $this->popularItems = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->unit_price,
                    'image' => $item->image_url ?? 'https://media.istockphoto.com/id/636061768/vector/modern-photograph-or-picture-icon-with-long-shadow.jpg?s=2048x2048&w=is&k=20&c=9zDG41z3ZrXk0hltnK4GFGe8EdKph2MtszvhIRKmifQ=',
                    'category' => $item->item_type->name ?? 'Uncategorized',
                    'description' => $item->description,
                   
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to load popular items: ' . $e->getMessage());
            $this->popularItems = [];
        }
    }

    protected function loadCategories()
    {
        try {
            $categories = ItemType::orderBy('name')->get();

            $this->categories = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'active' => $this->selectedCategory == $category->id,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to load categories: ' . $e->getMessage());
            $this->categories = [];
        }
    }

    public function setCategory($categoryId)
    {
        if ($categoryId === 'null' || $categoryId === null) {
            $this->selectedCategory = null;
        } else {
            $this->selectedCategory = $categoryId == $this->selectedCategory ? null : (int)$categoryId;
        }

        $this->loadPopularItems();
    }

    // Update the existing updatedSearch method
    public function updatedSearch($value)
    {
        $this->searching = true;

        // Add a small delay to prevent too many queries
        usleep(300000); // 300ms delay

        $this->loadPopularItems();
        $this->searching = false;
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
        try {
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
        } catch (\Exception $e) {
            Log::error('Failed to add item to cart: ' . $e->getMessage());
            $this->showAlert('error', 'Failed to add item to cart', 'error');
        }
    }

    public function removeFromCart($itemId)
    {
        if (isset($this->cartItems[$itemId])) {
            $itemName = $this->cartItems[$itemId]['name'];
            unset($this->cartItems[$itemId]);
            $this->updateCartTotals();
           
        }
    }

    public function incrementQuantity($itemId)
    {
        if (isset($this->cartItems[$itemId])) {
            $this->cartItems[$itemId]['quantity'] += 1;
            $this->updateCartTotals();
            $this->dispatch('itemAdded');
        }
    }

    public function decrementQuantity($itemId)
    {
        if (isset($this->cartItems[$itemId])) {
            if ($this->cartItems[$itemId]['quantity'] > 1) {
                $this->cartItems[$itemId]['quantity'] -= 1;
                $this->updateCartTotals();
                $this->dispatch('itemAdded');
            } else {
                $this->removeFromCart($itemId);
            }
        }
    }

    protected function updateCartTotals()
    {
        $this->totalItems = collect($this->cartItems)->sum('quantity');
        $this->totalAmount = collect($this->cartItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function holdTransaction()
    {
        if (empty($this->cartItems)) {
            $this->showAlert('error', 'Cannot hold empty transaction', 'error');
            return;
        }

        try {
            DB::transaction(function () {
                $transaction = Transactions::create([
                    'transaction_code' => $this->transactionCode,
                    'amount' => $this->totalAmount,
                    'type' => 'hold',
                    'customer_id' => $this->selectedCustomer,
                    'response' => json_encode([
                        'cart_items' => $this->cartItems,
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
            $this->loadHoldTransactions();
            $this->showAlert('success', 'Transaction has been held successfully', 'success');
        } catch (\Exception $e) {
            Log::error('Failed to hold transaction: ' . $e->getMessage());
            $this->showAlert('error', 'Failed to hold transaction', 'error');
        }
    }

    public function loadHoldTransactions()
    {
        try {
            $this->holdTransactions = Transactions::where('type', 'hold')
                ->latest()
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'code' => $transaction->transaction_code,
                        'amount' => $transaction->amount,
                        'customer' => $transaction->customer->name ?? 'Walk-in',
                        'items' => $transaction->salesItems->count(),
                        'date' => $transaction->created_at->format('M d, Y h:i A'),
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to load hold transactions: ' . $e->getMessage());
            $this->holdTransactions = [];
        }
    }

    public function loadHoldTransaction($transactionId)
    {
        try {
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

            $this->selectedHoldTransaction = $transactionId;
            $this->updateCartTotals();

            $this->showAlert('success', 'Hold transaction loaded', 'success');
        } catch (\Exception $e) {
            Log::error('Failed to load hold transaction: ' . $e->getMessage());
            $this->showAlert('error', 'Failed to load hold transaction', 'error');
        }
    }

    public function processPayment($method)
    {
        $this->paymentMethod = $method;

        if ($method != 'cash') {
            $this->amountTendered = $this->totalAmount;
        }
    }

    public function confirmPayment()
    {
        $this->validate($this->getPaymentValidationRules());

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
            Log::error('Payment processing failed: ' . $e->getMessage());
            $this->addError('paymentError', $e->getMessage());
            $this->showAlert('error', 'Payment processing failed: ' . $e->getMessage(), 'error');
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
                'mpesa_number' => $paymentData['mpesa_number'] ?? null,
                'split_details' => $paymentData['details'] ?? null,
                'cart_items' => $this->cartItems,
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

        $action = 'Cash Payment Record';

        $description = "Successfully created $transaction->type of amount $transaction->amount";

        User::saveAuditTrail($action, $description);

        $this->showAlert('success', $action, $description);

        return $this->redirect(route('pos'), navigate: true);
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
            'subtotal' => $this->totalAmount,
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
        $this->selectedCustomer = null;
        $this->customerSearch = '';
        $this->selectedHoldTransaction = null;
        $this->amountTendered = 0;
        $this->mpesaNumber = '';
        $this->splitPayment = false;
        $this->splitAmount1 = 0;
        $this->splitAmount2 = 0;
        $this->generateTransactionCode();
        $this->dispatch('itemAdded');
        $this->showPaymentPage = false;
        $this->showReceipt = false;
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

    public function focusSearch()
    {
        $this->dispatch('focus-search-input');
    }


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
        $this->loadPopularItems();
        // Debug output
        if ($this->debug) {
            Log::info('ShopPos render', [
                'popularItems_count' => count($this->popularItems),
                'search' => $this->search,
                'selectedCategory' => $this->selectedCategory,
            ]);
        }

        return view('livewire.shop-pos', [
            'categories' => $this->categories,
            'popularItems' => $this->popularItems,

            'searching' => $this->searching,
        ]);
    }

    // Debug method to test database connection
    public function testDatabase()
    {
        try {
            $count = Items::count();
            $categories = ItemType::count();

            Log::info('Database test', [
                'items_count' => $count,
                'categories_count' => $categories,
            ]);

            $this->showAlert('info', "Database test: Found $count items and $categories categories");
        } catch (\Exception $e) {
            Log::error('Database test failed: ' . $e->getMessage());
            $this->showAlert('error', 'Database connection failed: ' . $e->getMessage());
        }
    }
}
