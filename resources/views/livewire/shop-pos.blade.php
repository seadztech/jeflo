<div class="w-full bg-white rounded shadow-md border-1 border-primary p-4">


    <x-volt-livewire::spinner-component />

    @if (!$showPaymentPage)
    <div class="flex flex-col  space-x-0 md:space-x-4 md:flex-row ">
        <!-- Left Side - Item Selection (60% width) -->
        <div class="w-full mb-4 md:w-[60%] md:mb-0 bg-gray-200">
            <!-- Your existing item search component -->
            <div>
                <div " class=" relative">
                    <!-- Your existing search implementation -->
                </div>
            </div>

            <div class="flex p-2 mt-4 space-x-2 overflow-x-auto  rounded-lg">
                @foreach ($categories as $category)
                @php
                // Debug info
                // \Log::info("Rendering category: " . $category['name'] .
                // ", ID: " . ($category['id'] === null ? 'null' : $category['id']) .
                // ", Active: " . ($category['active'] ? 'true' : 'false'));
                @endphp

                <button wire:click="setCategory({{ $category['id'] === null ? 'null' : $category['id'] }})"
                    class="px-4 py-2 text-sm font-medium rounded-full whitespace-nowrap focus:outline-none transition-colors duration-200
                    {{ $category['active'] ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-white text-gray-700 hover:bg-gray-200' }}">
                    {{ $category['name'] }}
                    @if($category['active'])
                    <span class="ml-1">✓</span>
                    @endif
                </button>
                @endforeach
            </div>

            <!-- Items Grid Container with Vertical Scroll -->
            <div class="relative h-[calc(100vh-300px)] mt-4"> <!-- Increased height for better scrolling -->
                <!-- Grid Layout with Vertical Scroll -->
                <div class="grid grid-cols-2 gap-4 pb-4 overflow-y-auto sm:grid-cols-3 lg:grid-cols-4 border border-primary p-4" style="max-height: 100%;">
                    @foreach ($popularItems as $item)
                    <div wire:click="addToCart({{ $item['id'] }})"
                        class="p-3 border border-primary rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150 ease-in-out">
                        <div class="w-full h-32 mb-2 overflow-hidden bg-gray-200 rounded-md">
                            <img class='w-full' src="{{ $item['image'] ?? 'https://media.istockphoto.com/id/636061768/vector/modern-photograph-or-picture-icon-with-long-shadow.jpg?s=2048x2048&w=is&k=20&c=9zDG41z3ZrXk0hltnK4GFGe8EdKph2MtszvhIRKmifQ=' }}"
                                alt="{{ $item['name'] }}" class="object-cover w-full h-full" loading="lazy">
                        </div>
                        <div class="mt-auto">
                            <h3 class="font-medium text-gray-800 truncate">{{ $item['name'] }}</h3>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-sm font-semibold text-blue-600">
                                    Ksh {{ number_format($item['price'], 2) }}
                                </span>
                                <button class="p-1 text-white bg-blue-500 rounded-full hover:bg-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Side - Selling Point (40% width) -->
        <div class="w-full p-4 bg-gray-50 rounded-lg shadow md:w-[40%]">

            <!-- Cart Items -->
            <div class="border rounded-lg mb-4 max-h-[40vh] overflow-y-auto">
                <div class="p-2 font-medium text-gray-700 bg-gray-100 border-b">Cart Items</div>

                @if (count($cartItems))
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase">
                        <tr>
                            <th class="px-4 py-2">Item</th>
                            <th class="px-4 py-2 text-center">Qty</th>
                            <th class="px-4 py-2 text-right">Price</th>
                            <th class="px-4 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems as $item)
                        <tr class="border-b">
                            <td class="px-4 py-2 font-medium">{{ $item['name'] }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="inline-flex items-center space-x-2">
                                    <button wire:click="decrementQuantity({{ $item['id'] }})"
                                        class="p-1 border rounded text-gray-600 hover:text-black">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <span>{{ $item['quantity'] }}</span>
                                    <button wire:click="incrementQuantity({{ $item['id'] }})"
                                        class="p-1 border rounded text-gray-600 hover:text-black">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right">
                                Ksh {{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                            <td class="px-4 py-2 text-center">
                                <button wire:click="removeFromCart({{ $item['id'] }})"
                                    class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-4 text-center text-gray-500">Cart is empty</div>
                @endif
            </div>



            <!-- Summary -->
            <div class="pt-3 border-t mb-4">
                <div class="flex justify-between mb-1">
                    <span class="text-gray-700">Total Items:</span>
                    <span class="font-medium">{{ $totalItems }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span class="text-gray-800">Total Amount:</span>
                    <span class="text-blue-600">Ksh {{ number_format($totalAmount, 2) }}</span>
                </div>
            </div>


            <!-- Payment Button -->
            <button wire:click="makeSale"
                class="w-full px-4 py-3 mb-4 font-medium text-white bg-green-500 rounded-lg hover:bg-green-600">
                Process sale (Ksh {{ number_format($totalAmount, 2) }})
            </button>
            <div>


                <!-- Receipt Preview -->
                @if ($showReceipt && $lastTransaction)
                <livewire:receipt-preview :transactionId="$lastTransaction" />
                @endif
            </div>

        </div>

    </div>
    @else
    @include('includes.payments.compleatePayments')

    @endif




</div>