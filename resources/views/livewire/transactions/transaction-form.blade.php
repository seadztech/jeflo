<!-- resources/views/livewire/transactions/transaction-form.blade.php -->
<div class="p-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            <i class="fas fa-money-bill-transfer mr-2 text-blue-500"></i>
            Allocate Transaction #{{ $transaction->transaction_code }}
        </h1>
        @if($transaction)
        <p class="text-gray-600">
            <i class="fas fa-hashtag mr-1 text-gray-400"></i>
            {{ $transaction->transaction_code }} • 
            <i class="far fa-calendar-alt ml-2 mr-1 text-gray-400"></i>
            {{ $transaction->created_at->format('M d, Y H:i') }}
        </p>
        @endif
    </div>
    
    @if($transaction)
        <!-- Credit Payment Type Check -->
        @if($transaction->type === 'credit_payment')
            <!-- Credit Payment Message -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-credit-card text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-purple-800 mb-2">
                                <i class="fas fa-info-circle mr-2"></i>
                                Credit Payment Transaction
                            </h3>
                            <div class="text-gray-700 mb-3">
                                <p class="mb-2">This is a <span class="font-semibold text-purple-600">credit payment</span> transaction that has been <span class="font-semibold text-green-600">automatically allocated</span> to outstanding credit sales.</p>
                                <div class="bg-white rounded-lg p-4 border border-purple-100 mt-3">
                                    <div class="flex items-center text-purple-700 mb-2">
                                        <i class="fas fa-lightbulb mr-2"></i>
                                        <span class="font-medium">How it works:</span>
                                    </div>
                                    <ul class="text-sm text-gray-600 space-y-1 ml-6">
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                                            <span>Credit payments are automatically matched to overdue credit sales</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-robot text-blue-500 mr-2 mt-1"></i>
                                            <span>The system handles allocation intelligently in the background</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-eye-slash text-gray-500 mr-2 mt-1"></i>
                                            <span>No manual allocation is needed for this transaction type</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            @if($totalAllocated > 0)
                                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center text-green-800 mb-2">
                                        <i class="fas fa-check-double mr-2"></i>
                                        <span class="font-semibold">Allocation Summary</span>
                                    </div>
                                    <p class="text-green-700">
                                        This payment of <span class="font-bold">Ksh {{ number_format($transaction->amount, 2) }}</span> 
                                        has been automatically allocated to <span class="font-bold">{{ $allocations->count() }}</span> 
                                        credit sale(s).
                                    </p>
                                </div>
                            @endif
                            
                            <div class="mt-4 flex items-center text-sm text-blue-600">
                                <i class="fas fa-question-circle mr-2"></i>
                                <span>Need to review allocations? Check the transaction details or contact support.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Simplified View for Credit Payments -->
            <div class="bg-white rounded-xl shadow p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-purple-100 to-blue-100 mb-6">
                    <i class="fas fa-check-circle text-3xl text-purple-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Automatic Allocation Complete</h3>
                <p class="text-gray-600 text-lg mb-6 max-w-2xl mx-auto">
                    Credit payment transactions are processed automatically. The system has already allocated 
                    <span class="font-semibold text-purple-600">Ksh {{ number_format($transaction->amount, 2) }}</span> 
                    to the appropriate credit sales.
                </p>
                
                <!-- Show existing allocations -->
                @if($allocations->count() > 0)
                    <div class="mt-8">
                        <h4 class="font-semibold text-gray-700 mb-4 text-left">
                            <i class="fas fa-list-check mr-2 text-purple-500"></i>
                            Automatic Allocations ({{ $allocations->count() }})
                        </h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="text-left p-3 text-sm font-medium text-gray-700">Sale ID</th>
                                            <th class="text-left p-3 text-sm font-medium text-gray-700">Amount</th>
                                            <th class="text-left p-3 text-sm font-medium text-gray-700">Date</th>
                                            <th class="text-left p-3 text-sm font-medium text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($allocations as $allocation)
                                        <tr class="hover:bg-gray-50">
                                            <td class="p-3">
                                                <span class="font-medium">#{{ $allocation->sale_id }}</span>
                                            </td>
                                            <td class="p-3">
                                                <span class="text-green-600 font-semibold">
                                                    Ksh {{ number_format($allocation->amount, 2) }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-sm text-gray-600">
                                                {{ $allocation->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="p-3">
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Allocated
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="mt-8">
                    <a href="{{ route('transactions') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-blue-700 transition-all">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Transactions
                    </a>
                </div>
            </div>
        @else
            <!-- Regular Transaction (Non-Credit Payment) -->
            <!-- Transaction Stats Cards - Side by Side -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Total Amount Card -->
                <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
                    <div class="flex items-center mb-3">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-700">Total Amount</h3>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-800 mb-1">
                        Ksh {{ number_format($transaction->amount, 2) }}
                    </div>
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-credit-card mr-1"></i>
                        Transaction total
                    </p>
                </div>
                
                <!-- Allocated Amount Card -->
                <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
                    <div class="flex items-center mb-3">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-700">Allocated</h3>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-green-600 mb-1">
                        Ksh {{ number_format($totalAllocated, 2) }}
                    </div>
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-tasks mr-1"></i>
                        {{ $allocations->count() }} allocation(s)
                    </p>
                </div>
                
                <!-- Remaining Amount Card -->
                <div class="bg-white rounded-lg shadow p-5 border-l-4 
                    @if($remainingAmount > 0) border-yellow-500 @else border-green-500 @endif">
                    <div class="flex items-center mb-3">
                        <div class="@if($remainingAmount > 0) bg-yellow-100 @else bg-green-100 @endif p-3 rounded-full">
                            <i class="fas 
                                @if($remainingAmount > 0) fa-clock text-yellow-600 @else fa-check-double text-green-600 @endif 
                                text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-700">
                                @if($remainingAmount > 0) Remaining @else Complete @endif
                            </h3>
                        </div>
                    </div>
                    <div class="text-2xl font-bold 
                        @if($remainingAmount > 0) text-yellow-600 @else text-green-600 @endif mb-1">
                        Ksh {{ number_format($remainingAmount, 2) }}
                    </div>
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-percentage mr-1"></i>
                        {{ $transaction ? round(($totalAllocated / $transaction->amount) * 100, 1) : 0 }}% allocated
                    </p>
                </div>
            </div>
            
            <!-- Main Content Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="flex flex-row-reverse">
                    @if($remainingAmount > 0)
                    <div class="bg-white rounded-lg shadow">
                        <div class="border-b p-5">
                            <h2 class="font-semibold text-gray-800 text-lg">
                                <i class="fas fa-plus-circle mr-2 text-green-500"></i>
                                Add New Allocation
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Remaining amount: <span class="font-bold text-yellow-600">Ksh {{ number_format($remainingAmount, 2) }}</span>
                            </p>
                        </div>
                        
                        <div class="p-5">
                            <!-- Sale Selection Input -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-store mr-1"></i>
                                    Select Sale to Allocate
                                    @if($availableSales->count() > 0)
                                    <span class="text-gray-500 text-sm font-normal ml-2">
                                        ({{ $availableSales->count() }} available)
                                    </span>
                                    @endif
                                </label>
                                
                                <div class="relative">
                                    <select wire:model.live="selectedSaleId"
                                            class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white">
                                        <option value="">-- Select a sale --</option>
                                        @foreach($availableSales as $sale)
                                        <option value="{{ $sale->id }}">
                                            Sale #{{ $sale->id }} - 
                                            Ksh {{ number_format($sale->total_amount, 2) }} - 
                                            {{ ucfirst($sale->status) }} - 
                                            {{ $sale->created_at->format('M d, Y') }}
                                            (Available: Ksh {{ number_format($sale->unallocated_amount, 2) }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <i class="fas fa-caret-down text-gray-400"></i>
                                    </div>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                                
                                <!-- Search Input -->
                                <div class="mt-3">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-search text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                               wire:model.live="searchQuery"
                                               placeholder="Search sales by ID or amount..."
                                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    @if($searchQuery)
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-filter mr-1"></i>
                                        Filtered by: "{{ $searchQuery }}"
                                    </p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Allocation Form -->
                            @if($selectedSaleId)
                                @php
                                    $selectedSale = \App\Models\Sale::find($selectedSaleId);
                                @endphp
                                
                                <div class="border-t pt-6">
                                    <!-- Selected Sale Info -->
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                        <div class="flex items-start">
                                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                                <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 mb-1">Selected Sale Details</h4>
                                                <div class="grid grid-cols-2 gap-3 text-sm">
                                                    <div>
                                                        <span class="text-gray-600">Sale ID:</span>
                                                        <span class="font-medium ml-2">#{{ $selectedSaleId }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Status:</span>
                                                        <span class="ml-2">
                                                            @if($selectedSale->status == 'completed')
                                                                <span class="text-green-600">
                                                                    <i class="fas fa-check-circle mr-1"></i>
                                                                    {{ ucfirst($selectedSale->status) }}
                                                                </span>
                                                            @elseif($selectedSale->status == 'pending')
                                                                <span class="text-yellow-600">
                                                                    <i class="fas fa-clock mr-1"></i>
                                                                    {{ ucfirst($selectedSale->status) }}
                                                                </span>
                                                            @else
                                                                <span class="text-red-600">
                                                                    <i class="fas fa-times-circle mr-1"></i>
                                                                    {{ ucfirst($selectedSale->status) }}
                                                                </span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Total Amount:</span>
                                                        <span class="font-medium text-green-600 ml-2">
                                                            Ksh {{ number_format($selectedSale->total_amount, 2) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Available:</span>
                                                        <span class="font-medium text-blue-600 ml-2">
                                                            Ksh {{ number_format($selectedSale->unallocated_amount, 2) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Date:</span>
                                                        <span class="font-medium ml-2">
                                                            {{ $selectedSale->created_at->format('M d, Y H:i') }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Payment:</span>
                                                        <span class="font-medium ml-2">
                                                            {{ ucfirst($selectedSale->payment_method) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Allocation Form Fields -->
                                    <div class="space-y-6">
                                        <!-- Amount Input -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-money-check-alt mr-1"></i>
                                                Amount to Allocate
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500">Ksh</span>
                                                </div>
                                                <input type="number" 
                                                       step="0.01"
                                                       wire:model="allocatedAmount"
                                                       min="0.01"
                                                       max="{{ $remainingAmount }}"
                                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                       placeholder="Enter amount">
                                            </div>
                                            @error('allocatedAmount') 
                                                <div class="mt-2 text-red-600 text-sm flex items-center">
                                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                                <span>
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Transaction remaining: Ksh {{ number_format($remainingAmount, 2) }}
                                                </span>
                                                <span>
                                                    Sale available: Ksh {{ number_format($selectedSale->unallocated_amount, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Notes Input -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-sticky-note mr-1"></i>
                                                Notes (Optional)
                                            </label>
                                            <textarea wire:model="allocationNotes"
                                                      rows="3"
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                      placeholder="Add allocation notes (e.g., 'Partial payment', 'Balance to follow', etc.)"></textarea>
                                        </div>
                                        
                                        <!-- Summary -->
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                            <h4 class="font-medium text-gray-900 mb-3">
                                                <i class="fas fa-calculator mr-2"></i>
                                                Allocation Summary
                                            </h4>
                                            <div class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Transaction Remaining:</span>
                                                    <span class="font-medium">Ksh {{ number_format($remainingAmount, 2) }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Allocation Amount:</span>
                                                    <span class="font-bold text-green-600">Ksh {{ number_format($allocatedAmount, 2) }}</span>
                                                </div>
                                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                                    <span class="text-gray-700 font-medium">After Allocation:</span>
                                                    <span class="font-medium text-blue-600">
                                                        Ksh {{ number_format($remainingAmount - $allocatedAmount, 2) }} remaining
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="flex space-x-3 pt-4">
                                            <button wire:click="$set('selectedSaleId', '')"
                                                    class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                                                <i class="fas fa-times mr-2"></i>
                                                Change Sale
                                            </button>
                                            <button wire:click="addAllocation"
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                                    class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                                                <span wire:loading.remove>
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    Allocate Ksh {{ number_format($allocatedAmount, 2) }}
                                                </span>
                                                <span wire:loading>
                                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                                    Processing...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- No Sale Selected -->
                                <div class="border-t pt-8 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                        <i class="fas fa-hand-pointer text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Sale</h3>
                                    <p class="text-gray-600 mb-4">
                                        Choose a sale from the dropdown above to start allocating funds.
                                    </p>
                                    @if($availableSales->count() > 0)
                                    <p class="text-sm text-blue-600">
                                        <i class="fas fa-lightbulb mr-1"></i>
                                        {{ $availableSales->count() }} sales available for allocation
                                    </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <!-- Fully Allocated Message -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-8 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
                                <i class="fas fa-check-circle text-3xl text-green-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-green-800 mb-2">
                                <i class="fas fa-trophy mr-2"></i>
                                Fully Allocated!
                            </h3>
                            <p class="text-gray-600 mb-4">
                                All Ksh {{ number_format($transaction->amount, 2) }} has been allocated to {{ $allocations->count() }} sale(s).
                            </p>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-6">
                                <p class="text-sm text-green-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    No further allocations can be added to this transaction.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div>
                    <div class="bg-white rounded-lg shadow mb-6">
                        <div class="border-b p-5">
                            <div class="flex justify-between items-center">
                                <h2 class="font-semibold text-gray-800 text-lg">
                                    <i class="fas fa-list-check mr-2 text-blue-500"></i>
                                    Current Allocations
                                </h2>
                                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                                    <i class="fas fa-layer-group mr-1"></i>
                                    {{ $allocations->count() }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-5">
                            @if($allocations->count() > 0)
                                <div class="space-y-4">
                                    @foreach($allocations as $allocation)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-2">
                                                    <div class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-lg">
                                                        <i class="fas fa-tag mr-1"></i>
                                                        Sale #{{ $allocation->sale_id }}
                                                    </div>
                                                    <div class="ml-3 text-green-600 font-bold">
                                                        <i class="fas fa-money-bill-wave mr-1"></i>
                                                        Ksh {{ number_format($allocation->amount, 2) }}
                                                    </div>
                                                    <span class="ml-3 text-xs text-gray-500">
                                                        <i class="far fa-clock mr-1"></i>
                                                        {{ $allocation->created_at->format('M d, H:i') }}
                                                    </span>
                                                </div>
                                                
                                                <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                                                    <div>
                                                        <span class="text-gray-500">
                                                            <i class="fas fa-receipt mr-1"></i>
                                                            Sale Total:
                                                        </span>
                                                        <span class="font-medium ml-2">
                                                            Ksh {{ number_format($allocation->sale->total_amount, 2) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">
                                                            <i class="fas fa-balance-scale mr-1"></i>
                                                            Status:
                                                        </span>
                                                        <span class="ml-2">
                                                            @if($allocation->sale->status == 'completed')
                                                                <span class="text-green-600">
                                                                    <i class="fas fa-check-circle mr-1"></i>
                                                                    {{ ucfirst($allocation->sale->status) }}
                                                                </span>
                                                            @elseif($allocation->sale->status == 'pending')
                                                                <span class="text-yellow-600">
                                                                    <i class="fas fa-clock mr-1"></i>
                                                                    {{ ucfirst($allocation->sale->status) }}
                                                                </span>
                                                            @else
                                                                <span class="text-red-600">
                                                                    <i class="fas fa-times-circle mr-1"></i>
                                                                    {{ ucfirst($allocation->sale->status) }}
                                                                </span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                @if($allocation->notes)
                                                <div class="mt-3 p-3 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                                                    <p class="text-sm text-gray-700">
                                                        <i class="fas fa-sticky-note mr-2 text-blue-500"></i>
                                                        "{{ $allocation->notes }}"
                                                    </p>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <div class="ml-4 flex space-x-2">
                                                <button wire:click="editAllocation({{ $allocation->id }})"
                                                        class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50"
                                                        title="Edit allocation">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button wire:click="removeAllocation({{ $allocation->id }})"
                                                        class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50"
                                                        title="Remove allocation">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                        <i class="fas fa-inbox text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Allocations Yet</h3>
                                    <p class="text-gray-500 mb-4">This transaction hasn't been allocated to any sales.</p>
                                    @if($remainingAmount > 0)
                                    <p class="text-sm text-blue-600">
                                        <i class="fas fa-arrow-right mr-1"></i>
                                        Use the form on the right to add allocations
                                    </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- Transaction Not Found -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-100 mb-6">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-red-800 mb-2">
                    <i class="fas fa-search mr-2"></i>
                    Transaction Not Found
                </h3>
                <p class="text-gray-600 mb-4">
                    Transaction #{{ $transactionId }} does not exist in the system.
                </p>
                <a href="{{ route('transactions') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Transactions
                </a>
            </div>
        </div>
    @endif
</div>