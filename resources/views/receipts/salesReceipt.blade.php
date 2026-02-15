<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            width: 200px; /* approx 80mm */
            padding: 5px 8px;
            font-size: 8px;
            line-height: 1.2;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }

        .divider {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        .double-divider {
            border-top: 2px solid #000;
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.items td {
            padding: 2px 0;
        }

        .receipt-header {
            margin-bottom: 4px;
        }

        .business-name {
            font-size: 10px;
            font-weight: bold;
            margin: 2px 0;
        }

        .business-info {
            font-size: 7px;
            line-height: 1.1;
        }

        .receipt-info {
            margin: 4px 0;
        }

        .total-row {
            margin: 2px 0;
            display: flex;
            justify-content: space-between;
        }

        .footer {
            font-size: 6px;
            text-align: center;
            margin-top: 6px;
        }

        .thank-you {
            text-align: center;
            margin: 6px 0;
            font-style: italic;
        }

        .logo {
            width: 100%;
            max-width: 180px; /* fit receipt width */
            margin-bottom: 5px;
        }

        .credit {
            color: red;
            font-weight: bold;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <!-- Logo -->
    @if(file_exists(public_path('logo.png')))
        <div class="text-center">
            <img src="{{ public_path('logo.png') }}" alt="Logo" class="logo">
        </div>
    @endif

    <!-- Header -->
    <div class="text-center receipt-header">
        <div class="business-name">
            {{ $company->name ?? 'Seadztech' }}
        </div>
        <div class="business-info">
            @if($company?->address)
                Main Branch: {{ $company->address }}<br>
            @endif
            @if($company?->phone)
                Tel: {{ $company->phone }}<br>
            @endif
            @if($company?->email)
                Email: {{ $company->email }}<br>
            @endif
            Branch: {{ $sale->user->branch->name ?? 'Main Branch' }}
        </div>
    </div>

    <div class="divider"></div>

    <!-- Receipt Info -->
    @php
        $saleAmount = $sale->saleItems->sum(function($item){
            return $item->quantity * $item->unit_price;
        });
        $totalPaid = $sale->transactions->sum('amount');
        $balance = $saleAmount - $totalPaid;
    @endphp

    <div class="receipt-info">
        <div>Receipt #: {{ $sale->id }}</div>
        <div>Date: {{ $sale->created_at->format('d/m/Y H:i') }}</div>
        @if($totalPaid < $saleAmount)
            <div class="credit">** Credit Sale **</div>
        @else
            <div class="text-left bold">Paid in Full</div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Items Table -->
    <table class="items">
        <thead>
            <tr>
                <td class="text-left bold">Item</td>
                <td class="text-right bold" width="15%">Qty</td>
                <td class="text-right bold" width="25%">Price</td>
                <td class="text-right bold" width="25%">Total</td>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $item)
                @php
                    $lineTotal = $item->quantity * $item->unit_price;
                @endphp
                <tr>
                    <td>{{ substr($item->item->name ?? 'N/A', 0, 20) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0) }}</td>
                    <td class="text-right">{{ number_format($lineTotal, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="double-divider"></div>

    <!-- Totals -->
    <div class="total-row">
        <span class="bold">TOTAL:</span>
        <span class="bold">Ksh {{ number_format($saleAmount, 0) }}</span>
    </div>

    <div class="divider"></div>

    <!-- Payments -->
    <div class="bold">PAYMENTS:</div>
    @foreach($sale->transactions as $txn)
        <div class="total-row">
            <span>{{ ucfirst($txn->type == 'sms_mpesa' || $txn->type== 'mpesa' ? 'Mpesa' : 'Cash') }}:</span>
            <span>Ksh {{ number_format($txn->amount, 0) }}</span>
        </div>
    @endforeach

    <div class="total-row">
        <span class="bold">BALANCE:</span>
        <span class="bold">Ksh {{ number_format($balance, 0) }}</span>
    </div>

    <div class="total-row">
        <span>Served by:</span>
        <span>{{ $sale->user->name }}</span>
    </div>

    <!-- Footer -->
    <div class="thank-you">Thank you for your business!</div>

    <div class="footer">
        System by Seadztech Technologies | 0790651941 | https://seadztech.co.ke
    </div>
</body>
</html>
