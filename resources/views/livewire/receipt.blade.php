<div style="font-family: Arial, sans-serif; font-size: 14px; margin: 20px;">
    <!-- Header -->
    <x-volt-livewire::spinner-component />
    <div
        style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px;">
        <div>
            <img src="{{ asset('images/logo.png') }}" alt="Company Logo" style="height: 60px;">
        </div>
        <div style="text-align: right;">
            <h2 style="margin: 0;">Your Company Name</h2>
            <p style="margin: 0;">Receipt #{{ $sale->id }}</p>
            <p style="margin: 0;">Date: {{ $sale->created_at->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <!-- Table -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 8px; border: 1px solid #ccc;">Item</th>
                <th style="padding: 8px; border: 1px solid #ccc;">Qty</th>
                <th style="padding: 8px; border: 1px solid #ccc;">Unit Price (Ksh)</th>
                <th style="padding: 8px; border: 1px solid #ccc;">Total (Ksh)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->saleItems as $item)
                <tr>
                    <td style="padding: 8px; border: 1px solid #ccc;">{{ $item->item->name ?? 'N/A' }}</td>
                    <td style="padding: 8px; border: 1px solid #ccc;">{{ $item->quantity }}</td>
                    <td style="padding: 8px; border: 1px solid #ccc;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="padding: 8px; border: 1px solid #ccc;">
                        {{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold; padding: 8px; border: 1px solid #ccc;">
                    Total</td>
                <td style="font-weight: bold; padding: 8px; border: 1px solid #ccc;">
                    {{ number_format($sale->saleItems->sum(fn($i) => $i->quantity * $i->unit_price), 2) }} Ksh
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Payment -->
    <p style="margin-top: 20px;"><strong>Payments:</strong><br>
        @foreach ($sale->transactions as $txn)
            {{ ucfirst($txn->payment_method ?? 'Cash') }} - Ksh {{ number_format($txn->amount, 2) }}<br>
        @endforeach
    </p>

    <!-- Footer -->
    <p style="margin-top: 30px;">Thank you for your business!</p>

    <!-- Buttons -->
    <div style="margin-top: 30px;">
<a target="_blank" href="{{ route('sales.receipt.pdf', $sale->id) }}"> Print the receipt </a>

        {{-- <button onclick="window.print()"
            style="padding: 8px 12px; background-color: #3490dc; color: white; border: none; border-radius: 5px;">🖨️
            Print</button>
        <button wire:click="generateReceipt" class="btn btn-secondary"><i class="fa fa-share"></i> Generate
            Receipt</button>
        <a href="{{ url()->previous() }}"
            style="margin-left: 10px; padding: 8px 12px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px;">←
            Back</a>
    </div>

    <button wire:click="generateReceipt" wire:loading.attr="disabled">
        Print Receipt
    </button> --}}
</div>

<script>
    document.addEventListener('livewire:load', function() {
        Livewire.on('printReceipt', data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Create iframe for printing
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = data.pdfUrl;

            iframe.onload = function() {
                setTimeout(() => {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();

                    // Clean up after printing
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                        // Optionally delete the temp file via AJAX
                    }, 1000);
                }, 500);
            };

            document.body.appendChild(iframe);
        });
    });
</script>
