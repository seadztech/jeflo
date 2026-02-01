<div>
    <div class="mx-auto px-4 py-4">
        <!-- Spinner -->
        <x-volt-livewire::spinner-component />

        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-purple-600 via-blue-600 to-cyan-600 bg-clip-text text-transparent">
                        @if ($sale->status == 'pending')
                            Complete Sale
                        @else
                            Sale Receipt
                        @endif
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Sale #{{ $saleId ?? 'Pending' }} • {{ $sale->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                
                <div class="flex items-center gap-2">
                    @if($sale->status == 'pending')
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                            <i class="fas fa-clock mr-1"></i> Pending
                        </span>
                    @else
                        <span class="px-3 py-1 {{ $sale->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} text-xs font-medium rounded-full">
                            <i class="fas {{ $sale->payment_status == 'paid' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                            {{ ucfirst($sale->payment_status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

           <!-- Completed Sale View -->
        @if($sale->status === 'completed')
            <div class="mt-6 bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                Sale Completed Successfully!
                            </h2>
                            <p class="text-gray-600 mt-1">Sale #{{ $sale->id }} has been processed</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-2 bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-sm font-semibold rounded-full">
                                <i class="fas fa-check mr-1"></i>
                                {{ ucfirst($sale->payment_status) }}
                            </span>
                            <button wire:click="viewReceipt"
                                class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 flex items-center gap-2">
                                <i class="fas fa-print"></i>
                                Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Quick Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="p-4 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-200">
                            <p class="text-xs text-blue-500 mb-1">Payment Method</p>
                            <div class="flex items-center gap-2">
                                @if($sale->payment_method == 'cash')
                                    <i class="fas fa-money-bill-wave text-blue-600"></i>
                                @elseif($sale->payment_method == 'mpesa')
                                    <i class="fas fa-mobile-alt text-green-600"></i>
                                @else
                                    <i class="fas fa-credit-card text-yellow-600"></i>
                                @endif
                                <p class="text-lg font-bold text-gray-900 capitalize">{{ $sale->payment_method }}</p>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-gradient-to-br from-purple-50 to-white rounded-xl border border-purple-200">
                            <p class="text-xs text-purple-500 mb-1">Total Amount</p>
                            <p class="text-xl font-bold text-purple-700">
                                Ksh {{ number_format($sale->total_amount, 2) }}
                            </p>
                        </div>
                        
                        <div class="p-4 bg-gradient-to-br from-green-50 to-white rounded-xl border border-green-200">
                            <p class="text-xs text-green-500 mb-1">Amount Paid</p>
                            <p class="text-xl font-bold text-green-700">
                                Ksh {{ number_format($sale->amount_paid, 2) }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Receipt Actions -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <button wire:click="viewReceipt"
                            class="py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-medium flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                            <i class="fas fa-print"></i>
                            Print Receipt
                        </button>

                     

                        <a href="{{ route('pos') }}"
                            class="py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl hover:from-gray-700 hover:to-gray-800 transition-all duration-200 font-medium flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                            <i class="fas fa-home"></i>
                            Back to POS
                        </a>
                    </div>
                </div>
            </div>

            @else

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Customer & Transactions -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-user mr-2 text-blue-600"></i>
                                Customer Information
                            </h2>
                            <div class="flex gap-2">
                                @if(!$showCustomerForm)
                                    <button wire:click="toggleCustomerForm"
                                        class="px-3 py-1.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-xs rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow">
                                        <i class="fas {{ $sale->customer ? 'fa-exchange-alt' : 'fa-plus' }} text-xs"></i>
                                        {{ $sale->customer ? 'Change' : 'Add' }}
                                    </button>
                                    
                                    @if($sale->customer && !str_starts_with($sale->customer->phone_number, 'WALK-IN'))
                                        <button wire:click="editCustomer"
                                            class="px-3 py-1.5 bg-gradient-to-r from-yellow-600 to-yellow-700 text-white text-xs rounded-lg hover:from-yellow-700 hover:to-yellow-800 transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow">
                                            <i class="fas fa-edit text-xs"></i>
                                            Edit
                                        </button>
                                    @endif
                                    
                                    <button wire:click="setWalkInCustomer"
                                        class="px-3 py-1.5 bg-gradient-to-r from-gray-600 to-gray-700 text-white text-xs rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow">
                                        <i class="fas fa-walking text-xs"></i>
                                        Walk-in
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-5">
                        @if($showCustomerForm)
                            <!-- Customer Form -->
                            <div class="space-y-4">
                                @if(!$isEditingCustomer)
                                    <!-- Customer Search -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-search mr-1 text-gray-400"></i>
                                            Search Existing Customers
                                        </label>
                                        <div class="relative">
                                            <input type="text"
                                                wire:model.live="customerSearch"
                                                placeholder="Type name, phone, or email..."
                                                class="w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        </div>
                                        
                                        @if($customerSearch && strlen($customerSearch) > 2)
                                            <div class="mt-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg shadow-sm">
                                                @forelse($filteredCustomers as $customer)
                                                    <div wire:click="selectCustomer({{ $customer->id }})"
                                                        class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150">
                                                        <div class="flex justify-between items-center">
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2 mb-1">
                                                                    <p class="text-sm font-medium text-gray-900">{{ $customer->name }}</p>
                                                                    @if($customer->can_buy_on_credit)
                                                                        <span class="px-2 py-0.5 text-xs bg-gradient-to-r from-green-100 to-green-200 text-green-800 rounded-full">
                                                                            <i class="fas fa-credit-card mr-1"></i> Credit
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="flex items-center gap-3 text-xs text-gray-600">
                                                                    <span><i class="fas fa-phone-alt mr-1"></i>{{ $customer->phone_number }}</span>
                                                                    @if($customer->email)
                                                                        <span><i class="fas fa-envelope mr-1"></i>{{ $customer->email }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <i class="fas fa-chevron-right text-gray-400"></i>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="p-4 text-center">
                                                        <i class="fas fa-users text-gray-300 text-2xl mb-2"></i>
                                                        <p class="text-sm text-gray-500">No customers found</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="relative py-2">
                                        <div class="absolute inset-0 flex items-center">
                                            <div class="w-full border-t border-gray-300"></div>
                                        </div>
                                        <div class="relative flex justify-center">
                                            <span class="px-3 bg-white text-sm text-gray-500">OR</span>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Customer Form -->
                                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-200 p-5">
                                    <h3 class="font-semibold text-blue-800 text-lg mb-4">
                                        <i class="fas {{ $isEditingCustomer ? 'fa-user-edit' : 'fa-user-plus' }} mr-2"></i>
                                        {{ $isEditingCustomer ? 'Edit Customer' : 'Create New Customer' }}
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Full Name <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <input type="text" wire:model="customerName"
                                                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                        placeholder="John Doe">
                                                    <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                                </div>
                                                @error('customerName')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Phone Number <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <input type="text" wire:model="customerPhone"
                                                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                        placeholder="07XXXXXXXX">
                                                    <i class="fas fa-phone-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                                </div>
                                                @error('customerPhone')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Email Address
                                            </label>
                                            <div class="relative">
                                                <input type="email" wire:model="customerEmail"
                                                    class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                    placeholder="email@example.com">
                                                <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                            </div>
                                            @error('customerEmail')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Credit Limit (Ksh)
                                                </label>
                                                <div class="relative">
                                                    <input type="number" wire:model="creditLimit"
                                                        class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                                        placeholder="0.00">
                                                    <i class="fas fa-money-bill-wave absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                                </div>
                                                @error('creditLimit')
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            
                                            <div class="flex items-center space-x-3">
                                                <div class="flex items-center">
                                                    <input type="checkbox" wire:model="canBuyOnCredit"
                                                        id="canBuyOnCredit" class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                                                    <label for="canBuyOnCredit" class="ml-2 text-sm text-gray-700">
                                                        Allow Credit Purchases
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="pt-2">
                                            <button wire:click="saveCustomer"
                                                class="w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                                <i class="fas {{ $isEditingCustomer ? 'fa-save' : 'fa-user-plus' }}"></i>
                                                {{ $isEditingCustomer ? 'Update Customer' : 'Save Customer' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Current Customer Display -->
                            @if($sale->customer)
                                <div class="space-y-4">
                                    <div class="flex items-start justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200">
                                        <div class="flex-1">
                                            <div class="flex items-start gap-4">
                                                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg">
                                                    <i class="fas fa-user text-blue-600 text-xl"></i>
                                                </div>
                                                
                                                <div class="flex-1">
                                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                                        <h3 class="text-lg font-semibold text-gray-900">{{ $sale->customer->name }}</h3>
                                                        @if(str_starts_with($sale->customer->phone_number, 'WALK-IN'))
                                                            <span class="px-3 py-1 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 text-xs font-medium rounded-full">
                                                                <i class="fas fa-walking mr-1"></i> Walk-in
                                                            </span>
                                                        @elseif($sale->customer->can_buy_on_credit)
                                                            <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-xs font-medium rounded-full">
                                                                <i class="fas fa-credit-card mr-1"></i> Credit Customer
                                                            </span>
                                                        @else
                                                            <span class="px-3 py-1 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-xs font-medium rounded-full">
                                                                <i class="fas fa-money-bill-wave mr-1"></i> Cash Only
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="space-y-1 text-sm text-gray-600">
                                                        <div class="flex items-center gap-2">
                                                            <i class="fas fa-phone-alt w-4"></i>
                                                            <span>{{ $sale->customer->phone_number }}</span>
                                                        </div>
                                                        @if($sale->customer->email)
                                                            <div class="flex items-center gap-2">
                                                                <i class="fas fa-envelope w-4"></i>
                                                                <span>{{ $sale->customer->email }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Customer Credit Info -->
                                            @if($sale->customer->can_buy_on_credit && !str_starts_with($sale->customer->phone_number, 'WALK-IN'))
                                                <div class="mt-4 pt-4 border-t border-gray-200">
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                        <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-white rounded-lg border border-blue-100">
                                                            <p class="text-xs text-gray-500 mb-1">Credit Limit</p>
                                                            <p class="text-lg font-bold text-blue-700">
                                                                Ksh {{ number_format($sale->customer->credit_limit, 2) }}
                                                            </p>
                                                        </div>
                                                        
                                                        <div class="text-center p-3 bg-gradient-to-br {{ $sale->customer->current_balance > 0 ? 'from-red-50 to-white border-red-100' : 'from-green-50 to-white border-green-100' }} rounded-lg border">
                                                            <p class="text-xs text-gray-500 mb-1">Current Balance</p>
                                                            <p class="text-lg font-bold {{ $sale->customer->current_balance > 0 ? 'text-red-700' : 'text-green-700' }}">
                                                                Ksh {{ number_format($sale->customer->current_balance, 2) }}
                                                            </p>
                                                        </div>
                                                        
                                                        <div class="text-center p-3 bg-gradient-to-br {{ $availableCredit >= $totalAmount ? 'from-green-50 to-white border-green-100' : 'from-yellow-50 to-white border-yellow-100' }} rounded-lg border">
                                                            <p class="text-xs text-gray-500 mb-1">Available Credit</p>
                                                            <p class="text-lg font-bold {{ $availableCredit >= $totalAmount ? 'text-green-700' : 'text-yellow-700' }}">
                                                                Ksh {{ number_format($availableCredit, 2) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Credit Payment Section -->
                                                    @if($sale->customer->current_balance > 0)
                                                        <div class="mt-4">
                                                            @if(!$showCreditPaymentForm)
                                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-gradient-to-r from-purple-50 to-white rounded-lg border border-purple-200">
                                                                    <div class="flex items-center gap-2">
                                                                        <i class="fas fa-exclamation-triangle text-purple-600"></i>
                                                                        <div>
                                                                            <p class="text-sm font-medium text-gray-900">Outstanding Credit Balance</p>
                                                                            <p class="text-xs text-gray-600">Customer has pending credit payments</p>
                                                                        </div>
                                                                    </div>
                                                                    <button wire:click="toggleCreditPaymentForm"
                                                                        class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white text-sm rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200 flex items-center justify-center gap-2">
                                                                        <i class="fas fa-money-bill-wave"></i>
                                                                        Collect Payment
                                                                    </button>
                                                                </div>
                                                            @else
                                                                <div class="p-4 bg-gradient-to-br from-purple-50 to-white rounded-xl border border-purple-200">
                                                                    <div class="flex justify-between items-center mb-4">
                                                                        <h4 class="font-semibold text-purple-800">
                                                                            <i class="fas fa-money-check-alt mr-2"></i>
                                                                            Collect Credit Payment
                                                                        </h4>
                                                                        <button wire:click="toggleCreditPaymentForm"
                                                                            class="text-gray-500 hover:text-gray-700">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                    
                                                                    <div class="space-y-4">
                                                                        <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-200">
                                                                            <span class="text-sm font-medium text-gray-700">Current Balance:</span>
                                                                            <span class="text-lg font-bold text-red-600">
                                                                                Ksh {{ number_format($sale->customer->current_balance, 2) }}
                                                                            </span>
                                                                        </div>
                                                                        
                                                                        <div>
                                                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                                                Payment Amount (Ksh)
                                                                            </label>
                                                                            <div class="flex gap-2">
                                                                                <div class="relative flex-1">
                                                                                    <input type="number" wire:model="creditPaymentAmount"
                                                                                        class="w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                                                                        placeholder="Enter amount">
                                                                                    <i class="fas fa-coins absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                                                                </div>
                                                                                <button wire:click="clearFullCredit"
                                                                                    class="px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-200">
                                                                                    Full Amount
                                                                                </button>
                                                                            </div>
                                                                            @error('creditPaymentAmount')
                                                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                                            @enderror
                                                                        </div>
                                                                        
                                                                        <div class="flex gap-2">
                                                                            <button wire:click="recordCreditPayment"
                                                                                class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-medium rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                                                                <i class="fas fa-check"></i>
                                                                                Record Payment
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- No Customer Selected -->
                                <div class="text-center p-8 bg-gradient-to-br from-gray-50 to-white rounded-xl border-2 border-dashed border-gray-300">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-r from-gray-200 to-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user-plus text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Customer Selected</h3>
                                    <p class="text-gray-600 mb-4">Add a customer or select walk-in to proceed</p>
                                    <button wire:click="toggleCustomerForm"
                                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 inline-flex items-center gap-2">
                                        <i class="fas fa-plus"></i>
                                        Add Customer
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Transactions & Items Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Transactions Section -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                            <h2 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-exchange-alt mr-2 text-green-600"></i>
                                Available Transactions
                            </h2>
                            <p class="text-xs text-gray-500 mt-1">Select payments to apply to this sale</p>
                        </div>
                        
                        <div class="p-5">
                            @if(count($transactions) > 0)
                                <div class="space-y-2 max-h-72 overflow-y-auto pr-2">
                                    @foreach ($transactions as $trx)
                                        <div wire:click="toggleTransaction({{ $trx['id'] }})"
                                            class="p-4 rounded-lg border cursor-pointer transition-all duration-200 {{ in_array($trx['id'], $selectedTransactions) ? 'bg-gradient-to-r from-green-50 to-white border-green-300 ring-2 ring-green-200' : 'bg-white border-gray-200 hover:bg-gray-50 hover:border-gray-300' }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3 flex-1">
                                                    <div class="relative">
                                                        <input type="checkbox" 
                                                            {{ in_array($trx['id'], $selectedTransactions) ? 'checked' : '' }}
                                                            class="h-5 w-5 text-green-600 rounded focus:ring-green-500 border-gray-300">
                                                    </div>
                                                    
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span class="text-sm font-semibold text-gray-900">
                                                                #{{ $trx['transaction_code'] }}
                                                            </span>
                                                            @if($trx['type'] == 'credit_payment')
                                                                <span class="px-2 py-0.5 text-xs bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 rounded-full">
                                                                    <i class="fas fa-credit-card mr-1"></i> Credit Payment
                                                                </span>
                                                            @elseif($trx['type'] == 'cash')
                                                                <span class="px-2 py-0.5 text-xs bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 rounded-full">
                                                                    <i class="fas fa-money-bill-wave mr-1"></i> Cash
                                                                </span>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-xs text-gray-500">
                                                                <i class="far fa-clock mr-1"></i>
                                                                {{ \Carbon\Carbon::parse($trx['created_at'])->format('h:i A') }}
                                                            </span>
                                                            <span class="text-sm font-bold text-gray-900">
                                                                Ksh {{ number_format($trx['amount'], 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <button wire:click="deleteTransaction({{ $trx['id'] }})"
                                                    class="ml-2 p-2 text-gray-400 hover:text-red-600 rounded-full hover:bg-red-50 transition-colors duration-150"
                                                    title="Delete Transaction">
                                                    <i class="fas fa-trash-alt text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">Selected Payments:</span>
                                        <span class="font-semibold text-green-700">
                                            Ksh {{ number_format(collect($transactions)->whereIn('id', $selectedTransactions)->sum('amount'), 2) }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-r from-gray-200 to-gray-300 flex items-center justify-center">
                                        <i class="fas fa-exchange-alt text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Transactions Available</h3>
                                    <p class="text-gray-600">Record cash or M-PESA payments first</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sale Items Section -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                            <h2 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-shopping-cart mr-2 text-purple-600"></i>
                                Sale Items
                                <span class="ml-2 px-2 py-1 bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 text-xs font-medium rounded-full">
                                    {{ $totalItems }} items
                                </span>
                            </h2>
                        </div>
                        
                        <div class="p-5">
                            @if(count($saleItems) > 0)
                                <div class="space-y-3 max-h-72 overflow-y-auto pr-2">
                                    @foreach ($saleItems as $saleItem)
                                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-1">
                                                    <div class="p-2 bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg">
                                                        <i class="fas fa-box text-blue-600"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-sm font-medium text-gray-900">{{ $saleItem->item->name ?? 'N/A' }}</h4>
                                                        <p class="text-xs text-gray-500">
                                                            Unit Price: Ksh {{ number_format($saleItem->unit_price, 2) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-right">
                                                <div class="flex items-center gap-3">
                                                    <div class="text-center">
                                                        <span class="text-xs text-gray-500">Qty</span>
                                                        <p class="text-sm font-semibold text-gray-900">{{ $saleItem->quantity }}</p>
                                                    </div>
                                                    <div class="w-px h-8 bg-gray-200"></div>
                                                    <div class="text-center">
                                                        <span class="text-xs text-gray-500">Total</span>
                                                        <p class="text-sm font-bold text-blue-700">
                                                            Ksh {{ number_format($saleItem->total_price, 2) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-r from-gray-200 to-gray-300 flex items-center justify-center">
                                        <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Sale Items</h3>
                                    <p class="text-gray-600">Add items from the POS screen</p>
                                </div>
                            @endif
                            
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="text-center p-3 bg-gradient-to-br from-gray-50 to-white rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-500 mb-1">Subtotal</p>
                                        <p class="text-lg font-bold text-gray-900">
                                            Ksh {{ number_format($totalAmount + $discount, 2) }}
                                        </p>
                                    </div>
                                    <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-white rounded-lg border border-blue-200">
                                        <p class="text-xs text-blue-500 mb-1">Total Amount</p>
                                        <p class="text-lg font-bold text-blue-700">
                                            Ksh {{ number_format($totalAmount, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Payment & Summary -->
            <div class="space-y-6">
                <!-- Payment Summary Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-calculator mr-2 text-blue-600"></i>
                            Payment Summary
                        </h2>
                    </div>
                    
                    <div class="p-5">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-50 to-white rounded-lg border border-blue-200">
                                <span class="text-sm font-medium text-gray-700">Total Amount:</span>
                                <span class="text-xl font-bold text-blue-700">
                                    Ksh {{ number_format($totalAmount, 2) }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center p-3 bg-gradient-to-r {{ $amountReceived >= $totalAmount ? 'from-green-50 to-white border-green-200' : 'from-yellow-50 to-white border-yellow-200' }} rounded-lg border">
                                <span class="text-sm font-medium text-gray-700">Amount Received:</span>
                                <span class="text-lg font-bold {{ $amountReceived >= $totalAmount ? 'text-green-700' : 'text-yellow-700' }}">
                                    Ksh {{ number_format($amountReceived, 2) }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center p-3 bg-gradient-to-r {{ $balance > 0 ? 'from-red-50 to-white border-red-200' : 'from-green-50 to-white border-green-200' }} rounded-lg border">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ $balance > 0 ? 'Amount Due:' : 'Change:' }}
                                </span>
                                <span class="text-lg font-bold {{ $balance > 0 ? 'text-red-700' : 'text-green-700' }}">
                                    Ksh {{ number_format(abs($balance), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-credit-card mr-2 text-green-600"></i>
                            Payment Method
                        </h2>
                    </div>
                    
                    <div class="p-5">
                        <!-- Payment Method Selection -->
                        <div class="grid grid-cols-3 gap-2 mb-6">
                            <button type="button" wire:click="setPaymentMethod('cash')"
                                class="py-3 rounded-xl flex flex-col items-center justify-center gap-2 transition-all duration-200 {{ $paymentMethod == 'cash' ? 'bg-gradient-to-br from-blue-100 to-blue-50 border-2 border-blue-500 text-blue-700 shadow-sm' : 'bg-white border border-gray-300 text-gray-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600' }}">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                                <span class="text-xs font-medium">Cash</span>
                            </button>

                            <button type="button" wire:click="setPaymentMethod('mpesa')"
                                class="py-3 rounded-xl flex flex-col items-center justify-center gap-2 transition-all duration-200 {{ $paymentMethod == 'mpesa' ? 'bg-gradient-to-br from-green-100 to-green-50 border-2 border-green-500 text-green-700 shadow-sm' : 'bg-white border border-gray-300 text-gray-700 hover:bg-green-50 hover:border-green-300 hover:text-green-600' }}">
                                <i class="fas fa-mobile-alt text-xl"></i>
                                <span class="text-xs font-medium">M-PESA</span>
                            </button>

                            <button type="button" wire:click="setPaymentMethod('credit')"
                                class="py-3 rounded-xl flex flex-col items-center justify-center gap-2 transition-all duration-200 {{ $paymentMethod == 'credit' ? 'bg-gradient-to-br from-yellow-100 to-yellow-50 border-2 border-yellow-500 text-yellow-700 shadow-sm' : 'bg-white border border-gray-300 text-gray-700 hover:bg-yellow-50 hover:border-yellow-300 hover:text-yellow-600' }}">
                                <i class="fas fa-file-invoice-dollar text-xl"></i>
                                <span class="text-xs font-medium">Credit</span>
                            </button>
                        </div>

                        <!-- Payment Forms -->
                        <div class="space-y-4">
                            @if($paymentMethod == 'mpesa')
                                <div class="space-y-4">
                                    <div class="flex justify-center">
                                        <img class="h-8" src="{{ asset('mpesaLogo.png') }}" alt="M-PESA">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-phone-alt mr-1 text-gray-400"></i>
                                            M-PESA Phone Number
                                        </label>
                                        <input type="text" wire:model.defer="mpesaPhone" maxlength="12"
                                            placeholder="07XXXXXXXX"
                                            class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                                        @error('mpesaPhone')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button wire:click="sendSTK"
                                        class="w-full py-3.5 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                        <i class="fas fa-paper-plane"></i>
                                        Send STK Push Request
                                    </button>
                                </div>
                            @endif

                            @if($paymentMethod == 'cash')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-money-bill-wave mr-1 text-gray-400"></i>
                                            Cash Amount Received (Ksh)
                                        </label>
                                        <input type="number" step="0.01" wire:model="cashAmount"
                                            class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                            placeholder="0.00">
                                        @error('cashAmount')
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button wire:click="recordCashPayment"
                                        class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-medium shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        Record Cash Payment
                                    </button>
                                </div>
                            @endif

                            @if($paymentMethod == 'credit')
                                <div class="space-y-4">
                                    @if($sale && $sale->customer)
                                        <div class="p-4 bg-gradient-to-br from-yellow-50 to-white rounded-xl border border-yellow-200">
                                            <div class="space-y-4">
                                                <!-- Customer Credit Status -->
                                                <div class="flex items-center gap-3">
                                                    <div class="p-2 bg-gradient-to-br from-yellow-100 to-yellow-50 rounded-lg">
                                                        <i class="fas fa-user-check text-yellow-600"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-medium text-gray-900">{{ $sale->customer->name }}</h4>
                                                        <p class="text-xs text-gray-600">Credit Customer</p>
                                                    </div>
                                                </div>
                                                
                                                @if($sale->customer->can_buy_on_credit)
                                                    <!-- Credit Details -->
                                                    <div class="space-y-3">
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <div class="text-center p-2 bg-white rounded-lg border border-gray-200">
                                                                <p class="text-xs text-gray-500">Credit Limit</p>
                                                                <p class="text-sm font-bold text-gray-900">
                                                                    Ksh {{ number_format($sale->customer->credit_limit, 2) }}
                                                                </p>
                                                            </div>
                                                            <div class="text-center p-2 {{ $availableCredit >= $totalAmount ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} rounded-lg border">
                                                                <p class="text-xs text-gray-500">Available</p>
                                                                <p class="text-sm font-bold {{ $availableCredit >= $totalAmount ? 'text-green-700' : 'text-red-700' }}">
                                                                    Ksh {{ number_format($availableCredit, 2) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Credit Terms -->
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                                <i class="far fa-calendar-alt mr-1"></i>
                                                                Credit Terms
                                                            </label>
                                                            <div class="grid grid-cols-3 gap-2">
                                                                @foreach([15, 30, 60] as $days)
                                                                    <button type="button" wire:click="$set('creditDays', {{ $days }})"
                                                                        class="py-2 text-sm rounded-lg transition-all duration-200 {{ $creditDays == $days ? 'bg-gradient-to-r from-yellow-600 to-yellow-700 text-white shadow-md' : 'bg-white border border-gray-300 text-gray-700 hover:bg-yellow-50 hover:border-yellow-300' }}">
                                                                        {{ $days }} days
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                            <p class="text-xs text-gray-500 mt-2">
                                                                Due on <span class="font-medium">{{ now()->addDays($creditDays)->format('F d, Y') }}</span>
                                                            </p>
                                                        </div>
                                                        
                                                        <!-- Credit Status -->
                                                        @if($availableCredit >= $totalAmount)
                                                            <div class="p-3 bg-gradient-to-r from-green-50 to-white rounded-lg border border-green-200">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-check-circle text-green-600 text-lg mr-2"></i>
                                                                    <div>
                                                                        <p class="text-sm font-medium text-green-800">Credit Approved</p>
                                                                        <p class="text-xs text-green-600">Customer has sufficient credit limit</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="p-3 bg-gradient-to-r from-red-50 to-white rounded-lg border border-red-200">
                                                                <div class="flex items-center">
                                                                    <i class="fas fa-exclamation-circle text-red-600 text-lg mr-2"></i>
                                                                    <div>
                                                                        <p class="text-sm font-medium text-red-800">Insufficient Credit</p>
                                                                        <p class="text-xs text-red-600">
                                                                            Short by Ksh {{ number_format($totalAmount - $availableCredit, 2) }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="p-3 bg-gradient-to-r from-red-50 to-white rounded-lg border border-red-200">
                                                        <div class="flex items-center">
                                                            <i class="fas fa-exclamation-triangle text-red-600 text-lg mr-2"></i>
                                                            <div>
                                                                <p class="text-sm font-medium text-red-800">Not Approved for Credit</p>
                                                                <p class="text-xs text-red-600">This customer can only make cash purchases</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-4 bg-gradient-to-r from-red-50 to-white rounded-xl border border-red-200">
                                            <div class="flex items-center">
                                                <i class="fas fa-exclamation-triangle text-red-600 text-lg mr-3"></i>
                                                <div>
                                                    <p class="text-sm font-medium text-red-800">Select a Customer</p>
                                                    <p class="text-xs text-red-600">Please select a customer for credit sale</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Complete Sale Button -->
                @if($sale->status == 'pending')
                    @if(($paymentMethod != 'credit') || ($paymentMethod == 'credit' && $availableCredit >= $totalAmount && $sale && $sale->customer && $sale->customer->can_buy_on_credit))
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-5">
                                @error('paymentError')
                                    <div class="mb-4 p-3 bg-gradient-to-r from-red-50 to-white rounded-lg border border-red-200">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                                            <p class="text-sm text-red-800">{{ $message }}</p>
                                        </div>
                                    </div>
                                @enderror
                                
                                <button wire:click="confirmPayment"
                                    class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-700 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-800 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-3 text-lg">
                                    <i class="fas fa-check-circle"></i>
                                    @if($paymentMethod == 'credit')
                                        Complete Credit Sale
                                    @else
                                        Complete Sale
                                    @endif
                                </button>
                                
                                <p class="text-xs text-gray-500 text-center mt-3">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Click to finalize and process the sale
                                </p>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
        @endif


       

     
        <!-- JavaScript -->
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

        <script>
            Livewire.on('sale-completed', (data) => {
                // Confetti animation
                confetti({
                    particleCount: 150,
                    spread: 100,
                    origin: { y: 0.6 }
                });
                
                // Play success sound
                const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-correct-answer-tone-2870.mp3');
                audio.play().catch(e => console.log("Audio play failed:", e));
                
                // Scroll to top
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        </script>

     
    </div>
</div>