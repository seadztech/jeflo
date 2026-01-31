<div>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #receipt,
            #receipt * {
                visibility: visible;
            }

            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>

    <div class="p-4 mt-6 border rounded shadow" id="receipt">

        <h3 class="mb-2 text-xl font-bold text-center">RECEIPT</h3>
        <p><strong>Transaction Code:</strong> {{ $transaction->transaction_code }}</p>
        <p><strong>Date:</strong> {{ $transaction->created_at->format('d M Y H:i') }}</p>

        <table class="w-full mt-2 text-sm table-auto">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->salesItems as $sale)
                    <tr>
                        <td>{{ $sale->item->name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{{ number_format($sale->unit_price, 2) }}</td>
                        <td>{{ number_format($sale->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="mt-2 font-bold text-right">Total: KES {{ number_format($transaction->amount, 2) }}</p>

        <div class="mt-4 text-center">
            <button onclick="window.print()" class="px-4 py-2 text-white bg-blue-600 rounded">Print</button>
        </div>
    </div>
</div>
