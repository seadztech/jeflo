<div>
    <div class=" max-h-screen w-full bg-white rounded shadow-md border-1 border-primary p-4">
        <x-volt-livewire::spinner-component />

        @if (!$showPaymentPage)
        <div class="flex flex-col space-x-0 md:space-x-4 md:flex-row">
            <!-- Left Side - Item Selection (60% width) -->
            <div class="w-full mb-4 md:w-[60%] md:mb-0 bg-gray-100 p-4 rounded-lg">
                <!-- SEARCH SECTION -->
                <div class="mb-6">
                    <div class="relative">
                        <div class="flex items-center space-x-2 mb-4">
                            <!-- Main Search Input -->
                            <div class="relative flex-1">
                                <input
                                    type="text"
                                    wire:model="search"
                                    wire:keydown.debounce.300ms="updateSearch"
                                    placeholder="Search items by name, description, "
                                    class="w-full px-4 py-3 pl-10 pr-10 border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition duration-200"
                                    autocomplete="off">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                @if($search)
                                <button
                                    wire:click="$set('search', '')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                @endif
                            </div>

                          
                        </div>

                        <!-- Search Results Info -->
                        @if($search)
                        <div class="flex items-center justify-between px-2 py-1 text-sm">
                            <span class="text-gray-600">
                                @if(empty($popularItems))
                                No results for "<span class="font-semibold">{{ $search }}</span>"
                                @else
                                Found {{ count($popularItems) }} items for "<span class="font-semibold">{{ $search }}</span>"
                                @endif
                            </span>
                            @if($search && !empty($popularItems))
                            <button
                                wire:click="$set('search', '')"
                                class="px-2 py-1 text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors">
                                Clear search
                            </button>
                            @endif
                        </div>
                        @endif

                        <!-- Keyboard Shortcuts Info -->
                        <div class="flex items-center justify-between mt-2 text-xs text-gray-500 px-2">
                            <div class="flex items-center space-x-3">
                                <span class="flex items-center">
                                    <kbd class="px-1 py-0.5 bg-gray-200 rounded text-xs border border-gray-300">Ctrl</kbd>
                                    <span class="mx-1">+</span>
                                    <kbd class="px-1 py-0.5 bg-gray-200 rounded text-xs border border-gray-300">F</kbd>
                                    <span class="ml-1">to search</span>
                                </span>
                                <span class="flex items-center">
                                    <kbd class="px-1 py-0.5 bg-gray-200 rounded text-xs border border-gray-300">Esc</kbd>
                                    <span class="ml-1">to clear</span>
                                </span>
                            </div>
                            @if(!empty($popularItems))
                            <span class="text-gray-400">Press 1-9 to quick add items</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- CATEGORIES SECTION -->
                <div class="mb-6">
                    <h3 class="mb-3 text-sm font-semibold text-gray-700 uppercase">Categories</h3>
                    <div class="flex p-2 space-x-2 overflow-x-auto bg-white rounded-lg shadow-sm">
                        <!-- "All" Category -->
                        <button
                            wire:click="setCategory(null)"
                            class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap focus:outline-none transition-all duration-200 transform hover:-translate-y-0.5
                        {{ $selectedCategory === null ? 'bg-blue-600 text-white hover:bg-blue-700 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300 hover:border-blue-300' }}">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                All Items
                                @if($selectedCategory === null)
                                <span class="ml-1">✓</span>
                                @endif
                            </span>
                        </button>

                        @foreach ($categories as $category)
                        <button
                            wire:click="setCategory({{ $category['id'] }})"
                            class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap focus:outline-none transition-all duration-200 transform hover:-translate-y-0.5
                            {{ $selectedCategory == $category['id'] ? 'bg-blue-600 text-white hover:bg-blue-700 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300 hover:border-blue-300' }}">
                            <span class="flex items-center">
                                {{ $category['name'] }}
                                @if($selectedCategory == $category['id'])
                                <span class="ml-1">✓</span>
                                @endif
                            </span>
                        </button>
                        @endforeach
                    </div>

                    <!-- Active Filter Info -->
                    @if($selectedCategory !== null)
                    @php
                    $activeCategory = collect($categories)->firstWhere('id', $selectedCategory);
                    @endphp
                    @if($activeCategory)
                    <div class="flex items-center justify-between mt-3 px-3 py-2 bg-blue-50 rounded-lg border border-blue-200">
                        <span class="text-sm text-blue-700">
                            Filtering by: <span class="font-semibold">{{ $activeCategory['name'] }}</span>
                        </span>
                        <button
                            wire:click="setCategory(null)"
                            class="px-2 py-1 text-xs font-medium text-blue-600 bg-white border border-blue-300 rounded hover:bg-blue-50 transition-colors">
                            Clear filter
                        </button>
                    </div>
                    @endif
                    @endif
                </div>

                <!-- ITEMS GRID SECTION -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase">
                            @if($search)
                            Search Results
                            @elseif($selectedCategory !== null)
                            {{ collect($categories)->firstWhere('id', $selectedCategory)['name'] ?? 'Category' }} Items
                            @else
                            All Items
                            @endif
                        </h3>
                        <span class="px-2 py-1 text-xs font-medium text-gray-600 bg-gray-200 rounded">
                            {{ count($popularItems) }} items
                        </span>
                    </div>

                    <!-- Loading State -->
                    @if($searching)
                    <div class="flex items-center justify-center py-12">
                        <div class="text-center">
                            <div class="inline-block w-12 h-12 border-4 border-blue-200 rounded-full border-t-blue-600 animate-spin mb-3"></div>
                            <p class="text-gray-600">Searching items...</p>
                            <p class="text-sm text-gray-500 mt-1">Please wait</p>
                        </div>
                    </div>
                    @endif

                    <!-- Items Grid -->
                    <div class="relative h-[calc(100vh-400px)] overflow-y-auto border border-gray-200 rounded-lg bg-white p-4">
                        @if(empty($popularItems))
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">No items found</h4>
                            <p class="text-gray-500 max-w-md">
                                @if($search)
                                No items match your search "<span class="font-semibold">{{ $search }}</span>"
                                @else
                                No items available in this category
                                @endif
                            </p>
                            @if($search || $selectedCategory !== null)
                            <button
                                wire:click="setCategory(null); $set('search', '')"
                                class="mt-4 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                View all items
                            </button>
                            @endif
                        </div>
                        @else
                        <div class="grid grid-cols-2 gap-4 pb-4 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach ($popularItems as $index => $item)
                            <div wire:click="addToCart({{ $item['id'] }})"
                                data-item-id="{{ $item['id'] }}"
                                class="item-card group p-4 border border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 bg-white">
                                <!-- Item Image -->
                                <div class="relative w-full h-36 mb-3 overflow-hidden bg-gray-100 rounded-lg">
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        src="{{ $item['image'] ?? 'https://media.istockphoto.com/id/636061768/vector/modern-photograph-or-picture-icon-with-long-shadow.jpg?s=2048x2048&w=is&k=20&c=9zDG41z3ZrXk0hltnK4GFGe8EdKph2MtszvhIRKmifQ=' }}"
                                        alt="{{ $item['name'] }}"
                                        loading="lazy"
                                        >

                                    <!-- Quick Add Number -->
                                    <div class="absolute top-2 left-2 w-6 h-6 flex items-center justify-center text-xs font-bold text-white bg-blue-600 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                        {{ $index + 1 }}
                                    </div>

                                    <!-- Category Badge -->
                                    <div class="absolute top-2 right-2">
                                        <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-white bg-opacity-90 rounded-full">
                                            {{ $item['category'] }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Item Details -->
                                <div class="mt-auto">
                                    <!-- Item Name -->
                                    <h3 class="font-semibold text-gray-800 truncate mb-1" title="{{ $item['name'] }}">
                                        {{ $item['name'] }}
                                    </h3>

                                    <!-- Item Description -->
                                    @if(!empty($item['description']))
                                    <p class="text-xs text-gray-600 line-clamp-2 mb-2" title="{{ $item['description'] }}">
                                        {{ Str::limit($item['description'], 50) }}
                                    </p>
                                    @endif

                                    <!-- Price and Action -->
                                    <div class="flex items-center justify-between mt-2">
                                        <div>
                                            <span class="text-sm font-bold text-blue-700">
                                                Ksh {{ number_format($item['price'], 2) }}
                                            </span>
                                         
                                        </div>
                                        <button class="p-2 text-white bg-blue-600 rounded-full hover:bg-blue-700 transition-colors shadow-sm group-hover:shadow-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Hover Action Hint -->
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded-xl transition-all duration-200 pointer-events-none">
                                    <span class="px-3 py-1 text-xs font-medium text-white bg-black bg-opacity-75 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                        Click to add • Press {{ $index + 1 }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- Grid Tips -->
                    @if(!empty($popularItems))
                    <div class="mt-4 text-xs text-gray-500 text-center">
                        <p>💡 Tip: Click any item to add to cart, or press the number key (1-{{ min(9, count($popularItems)) }}) for quick add</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Side - Selling Point (40% width) -->
            <div class="w-full p-4 bg-gray-50 rounded-lg shadow md:w-[40%]">
                <!-- Cart Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Shopping Cart</h3>
                    @if(count($cartItems) > 0)
                    <button
                        wire:click="cancelTransaction"
                        wire:confirm="Are you sure you want to clear the cart?"
                        class="px-3 py-1 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                        Clear Cart
                    </button>
                    @endif
                </div>

                <!-- Cart Items -->
                <div class="border border-gray-300 rounded-lg mb-4 max-h-[40vh] overflow-y-auto bg-white">
                    <div class="sticky top-0 z-10 p-3 font-medium text-gray-700 bg-gray-100 border-b">
                        <div class="flex items-center justify-between">
                            <span>Cart Items ({{ $totalItems }})</span>
                            <span class="text-sm font-normal text-gray-600">
                                Total: <span class="font-semibold text-blue-600">Ksh {{ number_format($totalAmount, 2) }}</span>
                            </span>
                        </div>
                    </div>

                    @if (count($cartItems))
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-3">Item</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Price</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($cartItems as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <!-- <div class="flex-shrink-0 w-10 h-10 mr-3 overflow-hidden bg-gray-200 rounded">
                                                <img src="{{ $item['image'] }}" 
                                                     alt="{{ $item['name'] }}" 
                                                     class="object-cover w-full h-full">
                                            </div> -->
                                        <div>
                                            <div class="font-medium text-gray-800">{{ $item['name'] }}</div>
                                            <div class="text-xs text-gray-500">
                                                Ksh {{ number_format($item['price'], 2) }} each
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="inline-flex items-center space-x-2">
                                        <button wire:click="decrementQuantity({{ $item['id'] }})"
                                            class="p-1 border border-gray-300 rounded text-gray-600 hover:text-black hover:bg-gray-100 w-7 h-7 flex items-center justify-center"
                                            title="Decrease quantity">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <span class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                                        <button wire:click="incrementQuantity({{ $item['id'] }})"
                                            class="p-1 border border-gray-300 rounded text-gray-600 hover:text-black hover:bg-gray-100 w-7 h-7 flex items-center justify-center"
                                            title="Increase quantity">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-medium">
                                    Ksh {{ number_format($item['price'] * $item['quantity'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="removeFromCart({{ $item['id'] }})"
                                        class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded transition-colors"
                                        title="Remove item">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="flex flex-col items-center justify-center p-8 text-center">
                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h4 class="text-lg font-medium text-gray-700 mb-2">Your cart is empty</h4>
                        <p class="text-gray-500 max-w-sm">
                            Start by searching for items or browsing categories. Click on any item to add it to your cart.
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Cart Summary -->
                <div class="pt-4 border-t border-gray-300 mb-6">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Total Items:</span>
                            <span class="font-medium">{{ $totalItems }}</span>
                        </div>

                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-300">
                            <span class="text-gray-800">Total Amount:</span>
                            <span class="text-blue-600">Ksh {{ number_format($totalAmount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <!-- Process Sale Button -->
                    <button wire:click="makeSale"
                        @if(!count($cartItems)) disabled @endif
                        class="w-full px-4 py-3 font-medium text-white rounded-lg transition-all duration-200
                               @if(count($cartItems))
                                   bg-green-600 hover:bg-green-700 shadow-md hover:shadow-lg
                               @else
                                   bg-gray-400 cursor-not-allowed
                               @endif">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Process Sale
                            <span class="ml-2 font-bold">
                                (Ksh {{ number_format($totalAmount, 2) }})
                            </span>
                        </div>
                    </button>


                </div>

                <!-- Quick Stats -->
                <div class="mt-6 pt-4 border-t border-gray-300">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <div class="text-xs text-blue-600 uppercase font-medium mb-1">Items in Cart</div>
                            <div class="text-lg font-bold text-blue-700">{{ $totalItems }}</div>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <div class="text-xs text-green-600 uppercase font-medium mb-1">Total Value</div>
                            <div class="text-lg font-bold text-green-700">Ksh {{ number_format($totalAmount, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Receipt Preview -->
                @if ($showReceipt && $lastTransaction)
                <div class="mt-6">
                    <livewire:receipt-preview :transactionId="$lastTransaction" />
                </div>
                @endif
            </div>
        </div>
        @else
        @include('includes.payments.compleatePayments')
        @endif
    </div>

    <!-- Alpine.js for Keyboard Shortcuts -->
    <script>
        document.addEventListener('alpine:init', () => {
            // Keyboard shortcuts for POS
            document.addEventListener('keydown', (e) => {
                // Ctrl+F or / to focus search
                if ((e.ctrlKey && e.key === 'f') || e.key === '/') {
                    e.preventDefault();
                    const searchInput = document.querySelector('input[wire\\:model="search"]');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }

                // Escape clears search
                if (e.key === 'Escape') {
                    const searchInput = document.querySelector('input[wire\\:model="search"]');
                    if (searchInput && document.activeElement === searchInput) {
                        @this.set('search', '');
                    }
                }

                // Quick add by number (1-9)
                if (e.key >= '1' && e.key <= '9' && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    const index = parseInt(e.key) - 1;
                    const items = document.querySelectorAll('.item-card');
                    if (items[index]) {
                        const itemId = items[index].getAttribute('data-item-id');
                        @this.addToCart(itemId);
                    }
                }
            });
        });
    </script>

    <style>
        /* Custom scrollbar for cart */
        .max-h-\[40vh\]::-webkit-scrollbar {
            width: 6px;
        }

        .max-h-\[40vh\]::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .max-h-\[40vh\]::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .max-h-\[40vh\]::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Line clamp for descriptions */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Custom scrollbar for items grid */
        .h-\[calc\(100vh-400px\)\]::-webkit-scrollbar {
            width: 8px;
        }

        .h-\[calc\(100vh-400px\)\]::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .h-\[calc\(100vh-400px\)\]::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .h-\[calc\(100vh-400px\)\]::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    </style>
</div>