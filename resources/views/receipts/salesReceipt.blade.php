<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Professional Receipt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .receipt-container {
            max-width: 380px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .receipt-content {
            padding: 24px 20px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: 700; }
        .regular { font-weight: 400; }
        .medium { font-weight: 500; }

        .business-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f0f0f0;
        }

        .business-name {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .business-info {
            font-size: 12px;
            color: #666;
            line-height: 1.6;
        }

        .business-info i {
            color: #999;
            margin-right: 4px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: #e6f7e6;
            color: #0a7e0a;
        }

        .badge-warning {
            background: #fff3e0;
            color: #b45b0a;
        }

        .receipt-header {
            margin-bottom: 16px;
        }

        .receipt-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .receipt-meta {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #666;
            margin-bottom: 6px;
        }

        .divider {
            border-top: 1px dashed #ddd;
            margin: 16px 0;
        }

        .divider-solid {
            border-top: 2px solid #333;
            margin: 12px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        table.items th {
            text-align: left;
            padding: 8px 0;
            color: #666;
            font-weight: 600;
            font-size: 12px;
            border-bottom: 1px solid #eee;
        }

        table.items td {
            padding: 6px 0;
            color: #333;
        }

        table.items tr:last-child td {
            padding-bottom: 8px;
        }

        .product-name {
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .amount-large {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .total-section {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin: 12px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            font-size: 13px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px dashed #eee;
        }

        .payment-row:last-child {
            border-bottom: none;
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .payment-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #0a7e0a;
        }

        .payment-dot.cash {
            background: #f39c12;
        }

        .payment-dot.mpesa {
            background: #27ae60;
        }

        .balance-due {
            background: #fff3e0;
            padding: 10px;
            border-radius: 6px;
            margin: 12px 0;
            font-weight: 600;
            color: #b45b0a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .served-by {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 12px;
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #eee;
        }

        .thank-you {
            text-align: center;
            margin: 20px 0 10px;
            font-size: 14px;
            font-style: italic;
            color: #555;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            line-height: 1.5;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #eee;
        }

        .logo {
            max-width: 160px;
            margin-bottom: 12px;
        }

        .watermark {
            opacity: 0.5;
        }

        .credit-note {
            color: #d32f2f;
            font-weight: 600;
            margin: 8px 0;
            font-size: 13px;
        }

        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt-container {
                max-width: 100%;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-content">
            <!-- Logo -->
            @if(file_exists(public_path('logo.png')))
                <div class="text-center">
                    <img src="{{ public_path('logo.png') }}" alt="Logo" class="logo">
                </div>
            @endif

            <!-- Business Header -->
            <div class="business-header text-center">
                <div class="business-name">
                    {{ $company->name ?? 'SEADZTECH' }}
                </div>
                <div class="business-info">
                    @if($company?->address)
                        <div>📍 {{ $company->address }}</div>
                    @endif
                    @if($company?->phone)
                        <div>📞 {{ $company->phone }}</div>
                    @endif
                    @if($company?->email)
                        <div>✉️ {{ $company->email }}</div>
                    @endif
                    <div style="margin-top: 4px;">🏢 Branch: {{ $sale->user->branch->name ?? 'Main Branch' }}</div>
                </div>
            </div>

            <!-- Receipt Title & Status -->
            <div class="receipt-header">
                <div class="receipt-title">RECEIPT / INVOICE</div>
                <div class="receipt-meta">
                    <span>#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
                    <span>{{ $sale->created_at->format('d M Y, h:i A') }}</span>
                </div>
                
                @php
                    $saleAmount = $sale->saleItems->sum(function($item){
                        return $item->quantity * $item->unit_price;
                    });
                    $totalPaid = $sale->transactions->sum('amount');
                    $balance = $saleAmount - $totalPaid;
                @endphp

                @if($totalPaid < $saleAmount)
                    <div class="credit-note text-center">
                        ⚠️ PARTIAL PAYMENT - BALANCE DUE
                    </div>
                @else
                    <div class="text-center">
                        <span class="badge badge-success">✓ PAID IN FULL</span>
                    </div>
                @endif
            </div>

            <!-- Items Table -->
            <table class="items">
                <thead>
                    <tr>
                        <th class="text-left">Item</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->saleItems as $item)
                        @php
                            $lineTotal = $item->quantity * $item->unit_price;
                        @endphp
                        <tr>
                            <td class="product-name" title="{{ $item->item->name ?? 'N/A' }}">
                                {{ substr($item->item->name ?? 'N/A', 0, 25) }}
                                @if(strlen($item->item->name ?? '') > 25)…@endif
                            </td>
                            <td class="text-right">{{ number_format($item->quantity) }}</td>
                            <td class="text-right">KES {{ number_format($item->unit_price, 0) }}</td>
                            <td class="text-right">KES {{ number_format($lineTotal, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="divider-solid"></div>

            <!-- Totals -->
            <div class="total-section">
                <div class="total-row">
                    <span class="medium">Subtotal:</span>
                    <span class="medium">KES {{ number_format($saleAmount, 0) }}</span>
                </div>
                @if(isset($sale->discount) && $sale->discount > 0)
                    <div class="total-row">
                        <span class="medium">Discount:</span>
                        <span class="medium">- KES {{ number_format($sale->discount, 0) }}</span>
                    </div>
                @endif
                <div class="total-row" style="font-size: 16px; margin-top: 8px;">
                    <span class="bold">TOTAL AMOUNT:</span>
                    <span class="bold">KES {{ number_format($saleAmount, 0) }}</span>
                </div>
            </div>

            <!-- Payments -->
            <div style="margin: 16px 0;">
                <div class="medium" style="margin-bottom: 8px; color: #555;">PAYMENT DETAILS</div>
                
                @forelse($sale->transactions as $txn)
                    <div class="payment-row">
                        <div class="payment-method">
                            <span class="payment-dot {{ $txn->type == 'cash' ? 'cash' : 'mpesa' }}"></span>
                            <span>
                                @if(in_array($txn->type, ['sms_mpesa', 'mpesa']))
                                    M-PESA
                                @else
                                    {{ ucfirst($txn->type) }}
                                @endif
                            </span>
                        </div>
                        <span class="medium">KES {{ number_format($txn->amount, 0) }}</span>
                    </div>
                @empty
                    <div class="payment-row">
                        <span class="payment-method">No payments recorded</span>
                        <span>KES 0</span>
                    </div>
                @endforelse

                @if($balance > 0)
                    <div class="balance-due">
                        <span class="medium">BALANCE DUE</span>
                        <span class="bold">KES {{ number_format($balance, 0) }}</span>
                    </div>
                @endif
            </div>

            <!-- Served By -->
            <div class="served-by">
                <span>Served by: <span class="bold">{{ $sale->user->name }}</span></span>
                @if(isset($sale->payment_method))
                    <span>Method: {{ ucfirst($sale->payment_method) }}</span>
                @endif
            </div>

            <!-- Thank You Message -->
            <div class="thank-you">
                Thank you for your business! 🙏
            </div>

            <!-- Footer -->
            <div class="footer">
                <div>System by Seadztech Technologies</div>
                <div>📞 0790 651 941 | ✉️ info@seadztech.co.ke</div>
                <div>🌐 https://seadztech.co.ke</div>
                <div style="margin-top: 8px; font-size: 9px;">This is a computer generated receipt</div>
            </div>
        </div>
    </div>
</body>
</html>