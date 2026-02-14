<div class="space-y-6">

    {{-- CUSTOMER DETAILS --}}
    <div class="bg-white shadow rounded p-4">
        <h2 class="text-xl font-bold mb-3">Customer Details</h2>

        <div class="grid grid-cols-2 gap-4">
            <p><strong>Name:</strong> {{ $customer->name }}</p>
            <p><strong>Phone:</strong> {{ $customer->phone_number }}</p>
            <p><strong>Email:</strong> {{ $customer->email }}</p>
            <p><strong>Credit Limit:</strong> {{ number_format($customer->credit_limit, 2) }}</p>
            <p>
                <strong>Current Balance:</strong>
                <span class="text-red-600 font-bold">
                    {{ number_format($customer->current_balance, 2) }}
                </span>
            </p>
        </div>

        {{-- Footer --}}
        <div class="border-t mt-4 pt-3 flex justify-between font-bold">
            <span>Available Credit:</span>
            <span>
                {{ number_format($customer->credit_limit - $customer->current_balance, 2) }}
            </span>
        </div>
    </div>

    {{-- SALES TABLE --}}
    <div class="bg-white shadow rounded p-4">
        <h2 class="text-xl font-bold mb-3">Sales</h2>

        <table class="w-full border border-gray-300">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Total</th>
                    <th class="p-2 border">Paid</th>
                    <th class="p-2 border">Balance</th>
                    <th class="p-2 border">Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach($sales as $sale)
                    <tr class="border-t">
                        <td class="p-2 border">{{ $sale->id }}</td>
                        <td class="p-2 border">{{ $sale->created_at }}</td>
                        <td class="p-2 border">{{ number_format($sale->total_amount, 2) }}</td>
                        <td class="p-2 border">{{ number_format($sale->amount_paid, 2) }}</td>
                        <td class="p-2 border">{{ number_format($sale->balance_due, 2) }}</td>
                        <td class="p-2 border">{{ $sale->status }}</td>
                    </tr>
                @endforeach
            </tbody>

            {{-- Footer Totals --}}
            <tfoot class="bg-gray-100 font-bold">
                <tr>
                    <td class="p-2 border" colspan="2">Totals</td>
                    <td class="p-2 border">{{ number_format($salesTotals['total_sales'], 2) }}</td>
                    <td class="p-2 border">{{ number_format($salesTotals['total_paid'], 2) }}</td>
                    <td class="p-2 border">{{ number_format($salesTotals['total_balance'], 2) }}</td>
                    <td class="p-2 border"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- LEDGER / BALANCE SHEET --}}
    <div class="bg-white shadow rounded p-4">
        <h2 class="text-xl font-bold mb-3">Transaction Ledger</h2>

        <table class="w-full border border-gray-300">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Reference</th>
                    <th class="p-2 border">Type</th>
                    <th class="p-2 border">Debit</th>
                    <th class="p-2 border">Credit</th>
                    <th class="p-2 border">Balance</th>
                </tr>
            </thead>

            <tbody>
                @foreach($ledger as $row)
                    <tr class="border-t @if(!empty($row['is_opening'])) bg-yellow-50 @endif">
                        <td class="p-2 border">{{ $row['date'] }}</td>
                        <td class="p-2 border">{{ $row['reference'] }}</td>
                        <td class="p-2 border font-semibold">{{ $row['type'] }}</td>

                        <td class="p-2 border text-right">
                            {{ !empty($row['debit']) ? number_format($row['debit'], 2) : '' }}
                        </td>

                        <td class="p-2 border text-right">
                            {{ !empty($row['credit']) ? number_format($row['credit'], 2) : '' }}
                        </td>

                        <td class="p-2 border font-bold text-right">
                            {{ number_format($row['balance'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            {{-- Footer Totals --}}
            <tfoot class="bg-gray-100 font-bold">
                <tr>
                    <td class="p-2 border" colspan="3">Totals</td>
                    <td class="p-2 border text-right">{{ number_format($ledgerTotals['debit'], 2) }}</td>
                    <td class="p-2 border text-right">{{ number_format($ledgerTotals['credit'], 2) }}</td>
                    <td class="p-2 border text-right">{{ number_format($ledgerTotals['closing_balance'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
