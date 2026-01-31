<div class="max-w-screen-lg p-4 mx-auto bg-white rounded shadow-md">

    {{-- Search & Items --}}
    <input type="text" wire:model="search" placeholder="Search items..." class="w-full p-2 mb-4 border rounded">

    <div class="grid grid-cols-2 gap-4 mb-4 md:grid-cols-4">
        {{-- @foreach($items as $item) --}}
            <button wire:click="addToCart()" class="p-3 border rounded hover:bg-gray-200">
                {{-- <strong>{{ $item->name }}</strong><br> --}}
                {{-- <small>KES {{ number_format($item->unit_price, 2) }}</small> --}}
                <strong>Item 1</strong><br>
                <small>KES 10</small>
            </button>
        {{-- @endforeach --}}
    </div>

    {{-- Cart --}}

    <table class="w-full mb-4 table-auto">
        <thead>
            <tr class="bg-gray-100">
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {{-- @foreach($cart as $index => $item) --}}
                <tr>
                    {{-- <td>{{ $item['name'] }}</td>
                    <td>
                        <input type="number" min="1" wire:change="updateQuantity({{ $index }}, $event.target.value)" value="{{ $item['quantity'] }}" class="w-16 p-1 border">
                    </td>
                    <td>{{ number_format($item['unit_price'], 2) }}</td>
                    <td>{{ number_format($item['total_price'], 2) }}</td>
                    <td><button wire:click="removeFromCart({{ $index }})" class="text-red-500">x</button></td> --}}

                    <td>Name</td>
                    <td>
                        <input type="number" min="1" wire:change="updateQuantity()" value="" class="w-16 p-1 border">
                    </td>
                    <td>20</td>
                    <td>200.3 }}</td>
                    <td><button wire:click="" class="text-red-500">x</button></td>
                </tr>
            {{-- @endforeach --}}
        </tbody>
    </table>

    {{-- Transaction Summary --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <input type="text" wire:model="paymentCode" placeholder="Payment Code (optional)" class="p-2 border rounded">
        </div>
        <div class="text-lg font-bold">
            Total: KES 300
        </div>
    </div>

    {{-- Checkout --}}
    <div class="text-right">
        <button wire:click="completeSale" class="px-6 py-2 text-white bg-green-600 rounded">Complete Sale</button>
    </div>
    @endif

    {{-- Receipt Preview --}}
    {{-- @if($showReceipt && $lastTransaction)
        <livewire:receipt-preview :transactionId="$lastTransaction" />
    @endif --}}

</div>
