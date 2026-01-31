<div>
    <div class="h-screen flex flex-col bg-gray-50">
        <!-- Loading spinner -->
        <div class="absolute right-3 top-3 z-30">
            <x-volt-livewire::spinner-component />
        </div>

        <!-- Header - Reduced size -->
        <div class="flex-shrink-0 flex flex-col md:flex-row justify-between items-start md:items-center p-3 md:p-4 bg-white border-b">
            <div class="page-header-title mb-2 md:mb-0">
                <h4 class="mb-0 text-lg md:text-xl uppercase font-semibold bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 bg-clip-text text-transparent">
                    {{ strtoupper($title ?? 'Reports Dashboard') }}
                </h4>
                <p class="text-gray-600 text-xs mt-1">Generate comprehensive business insights and reports</p>
            </div>

            <div class="flex flex-wrap gap-1 mt-2 md:mt-0">
                <button wire:click="dashboard"
                    class="my-1 w-32 h-7 rounded-md bg-gradient-to-r from-purple-500 via-red-500 to-pink-500 text-white font-semibold hover:opacity-90 transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                    <i class="fa fa-reply text-xs"></i> DASHBOARD
                </button>

                @if(!empty($reportData))
                <button wire:click="exportToExcel"
                    class="h-7 px-2 rounded-md bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-all duration-300 flex items-center gap-2 text-sm">
                    <i class="fa fa-file-excel text-xs"></i> Excel
                </button>

                <button wire:click="exportToPDF"
                    class="h-7 px-2 rounded-md bg-red-600 text-white font-semibold hover:bg-red-700 transition-all duration-300 flex items-center gap-2 text-sm">
                    <i class="fa fa-file-pdf text-xs"></i> PDF
                </button>

                <button onclick="window.print()"
                    class="h-7 px-2 rounded-md bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-all duration-300 flex items-center gap-2 text-sm">
                    <i class="fa fa-print text-xs"></i> Print
                </button>
                @endif
            </div>
        </div>

       
        <!-- Main content area - Scrollable content -->
        <div class="flex-1 min-h-0 relative bg-white">
            <!-- Overlay background -->
            @if($isMenuOpen)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-40" wire:click="toggleMenu"></div>
            @endif

            <!-- Report generation form (centered overlay) -->
            @if($isMenuOpen)
            <div class="fixed inset-0 flex items-center justify-center z-50 p-4">
                <div class="bg-[#3F4E67] text-slate-100 shadow-lg flex-col border border-slate-200 w-full max-w-lg mx-auto rounded-lg max-h-[90vh] flex flex-col">
                    <!-- Card Header -->
                    <div class="flex-shrink-0 p-3 border-b border-slate-300 flex justify-between items-center bg-gradient-to-r from-slate-700 to-slate-800">
                        <h3 class="text-slate-50 text-base font-bold uppercase">Generate Report</h3>
                        <button wire:click="toggleMenu" class="text-slate-50 hover:text-white transition-colors">
                            <i class="fa fa-close text-lg"></i>
                        </button>
                    </div>

                    <!-- Form Fields -->
                    <form wire:submit.prevent="generateReport" class="flex-1 p-4 space-y-3 flex flex-col overflow-y-auto">
                        <!-- Branch -->
                        <div>
                            <label class="block text-xs font-medium mb-1 text-slate-200">Branch</label>
                            <select wire:model="selectedBranch"
                                class="h-9 w-full rounded-md text-slate-900 px-3 border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-sm">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Report Type -->
                        <div>
                            <label class="block text-xs font-medium mb-1 text-slate-200">Report Type</label>
                            <select wire:model="selectedReportType"
                                class="h-9 w-full rounded-md text-slate-900 px-3 border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-sm">
                                <option value="">Select report type</option>

                                <!-- Sales Reports -->
                                <optgroup label="📊 Sales Reports">
                                    <option value="complete_sales">Complete Sales</option>
                                    <option value="incomplete_sales">Incomplete Sales</option>
                                    <option value="daily_sales_summary">Daily Sales Summary</option>
                                    <option value="sales_by_category">Sales by Category</option>
                                    <option value="top_selling_items">Top Selling Items</option>
                                    <option value="hourly_sales_analysis">Hourly Sales Analysis</option>
                                </optgroup>

                                <!-- Financial Reports -->
                                <optgroup label="💰 Financial Reports">
                                    <option value="profit_loss">Profit & Loss Statement</option>
                                    <option value="payment_method_summary">Payment Methods</option>
                                    <option value="transactions">Transactions Report</option>
                                </optgroup>

                                <!-- Inventory Reports -->
                                <optgroup label="📦 Inventory Reports">
                                    <option value="items">Items Master List</option>
                                    <option value="stock_levels">Stock Levels</option>
                                    <option value="expiring_items">Expiring Items</option>
                                    <option value="wastage_report">Wastage Report</option>
                                </optgroup>

                                <!-- Customer Reports -->
                                <optgroup label="👥 Customer Reports">
                                    <option value="customers">Customer Database</option>
                                    <option value="customer_purchases">Customer Purchase History</option>
                                    <option value="loyalty_summary">Customer Loyalty</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium mb-1 text-slate-200">From Date</label>
                                <input type="date" wire:model="startDate"
                                    class="h-9 w-full rounded-md text-slate-900 px-3 border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-medium mb-1 text-slate-200">To Date</label>
                                <input type="date" wire:model="endDate"
                                    class="h-9 w-full rounded-md text-slate-900 px-3 border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-sm">
                            </div>
                        </div>

                        <!-- Quick Date Presets -->
                        <div class="pt-2">
                            <label class="block text-xs font-medium mb-2 text-slate-200">Quick Date Range</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                    wire:click="$set('startDate', '{{ now()->subDays(7)->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                                    class="px-2 py-1 text-xs bg-slate-600 hover:bg-slate-700 text-white rounded-md transition-colors text-[10px]">
                                    Last 7 Days
                                </button>
                                <button type="button"
                                    wire:click="$set('startDate', '{{ now()->subDays(30)->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                                    class="px-2 py-1 text-xs bg-slate-600 hover:bg-slate-700 text-white rounded-md transition-colors text-[10px]">
                                    Last 30 Days
                                </button>
                                <button type="button"
                                    wire:click="$set('startDate', '{{ now()->startOfMonth()->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}')"
                                    class="px-2 py-1 text-xs bg-slate-600 hover:bg-slate-700 text-white rounded-md transition-colors text-[10px]">
                                    This Month
                                </button>
                                <button type="button"
                                    wire:click="$set('startDate', '{{ now()->subMonth()->startOfMonth()->format('Y-m-d') }}'); $set('endDate', '{{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}')"
                                    class="px-2 py-1 text-xs bg-slate-600 hover:bg-slate-700 text-white rounded-md transition-colors text-[10px]">
                                    Last Month
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3">
                            <button type="submit"
                                class="w-full h-9 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-md font-semibold shadow hover:opacity-90 transition-all flex items-center justify-center gap-2 text-sm">
                                <i class="fa fa-chart-bar text-sm"></i>
                                Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Scrollable content area -->
            <div class="h-full w-full overflow-hidden">
                <div class="h-full flex flex-col">
                    <!-- Report header - Reduced size -->
                    <div class="flex-shrink-0 h-14 border border-gray-300 flex justify-between items-center px-3 bg-gradient-to-r from-gray-50 to-white">
                        @php
                        $icons = [
                        'Sales' => 'fa-shopping-cart',
                        'Profit' => 'fa-chart-line',
                        'Stock' => 'fa-boxes',
                        'Customer' => 'fa-users',
                        'Transaction' => 'fa-exchange-alt',
                        'Item' => 'fa-tag',
                        'Payment' => 'fa-credit-card',
                        'Daily' => 'fa-calendar-day',
                        'Hourly' => 'fa-clock',
                        'Staff' => 'fa-user-tie',
                        'Wastage' => 'fa-trash-alt',
                        'Loyalty' => 'fa-crown',
                        'Void/Refund' => 'fa-ban',
                        'Tax' => 'fa-receipt',
                        'Inventory' => 'fa-warehouse',
                        'Complete' => 'fa-check-circle',
                        'Incomplete' => 'fa-clock'
                        ];

                        $icon = 'fa-chart-bar'; // default
                        foreach($icons as $key => $value) {
                        if(stripos($reportTitle, $key) !== false) {
                        $icon = $value;
                        break;
                        }
                        }
                        @endphp

                        <h3 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2">
                            @if($reportTitle != '')
                            <i class="fas {{ $icon }} text-blue-600 text-sm"></i>
                            {{ $reportTitle }}
                            @else
                            <i class="fas fa-chart-bar text-gray-400 text-sm"></i>
                            No Report Generated
                            @endif
                        </h3>
                    </div>

                    <!-- Report content area - Scrollable -->
                    <div class="flex-1 min-h-0 overflow-auto p-3">
                        @if(empty($reportData))
                        <div class="flex flex-col items-center justify-center h-full">
                            <div class="relative">
                                <div class="w-24 h-24 border-4 border-dashed border-gray-300 rounded-full flex items-center justify-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-full flex items-center justify-center">
                                        <i class="fas fa-chart-pie text-2xl text-blue-400"></i>
                                    </div>
                                </div>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow">
                                    <i class="fas fa-bolt text-white text-xs"></i>
                                </div>
                            </div>
                            <p class="mt-4 text-lg font-bold text-gray-700">Ready for Insights?</p>
                            <p class="mt-1 text-gray-500 max-w-md text-center text-sm">Generate detailed reports to analyze sales, inventory, profits, and customer behavior</p>
                            <div class="mt-4 flex flex-wrap gap-2 justify-center">
                                <button wire:click="toggleMenu"
                                    class="px-4 py-1.5 bg-gradient-to-r from-blue-600 to-blue-800 text-white font-semibold rounded-md hover:opacity-90 transition-all duration-300 flex items-center gap-2 text-sm">
                                    <i class="fas fa-plus text-xs"></i> Generate Report
                                </button>
                                <button wire:click="generateQuickReport('profit_loss', 30)"
                                    class="px-4 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-700 text-white font-semibold rounded-md hover:opacity-90 transition-all duration-300 flex items-center gap-2 text-sm">
                                    <i class="fas fa-chart-line text-xs"></i> Quick P&L
                                </button>
                            </div>
                        </div>
                        @else
                        <!-- Report Summary Card - Reduced size -->
                        <div class="mb-3 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                            <div class="flex flex-wrap items-center justify-between">
                                <div>
                                    <h4 class="text-base font-bold text-gray-800">{{ $reportTitle }}</h4>
                                    <p class="text-xs text-gray-600">{{ count($reportData) }} records • {{ $startDate }} to {{ $endDate }}</p>
                                </div>
                                <div class="flex items-center gap-3 mt-1 md:mt-0">
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500">{{ $summaryTitle }}</div>
                                        <div class="text-sm font-bold text-blue-700">{{ $summaryValue }}</div>
                                    </div>
                                    @if(isset($totalAmount))
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500">Total Amount</div>
                                        <div class="text-sm font-bold text-emerald-700">KSH {{ number_format($totalAmount) }}</div>
                                    </div>
                                    @endif
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500">Records</div>
                                        <div class="text-sm font-bold text-purple-700">{{ $totalRecords }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data table with fixed header - Reduced padding -->
                        <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm flex flex-col h-[calc(100%-100px)]">
                            <div class="overflow-auto flex-1">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-gradient-to-r from-gray-700 to-gray-800 sticky top-0 z-10">
                                        <tr>
                                            @foreach($headers as $header)
                                            <th class="py-2 px-3 border-b text-left text-white font-semibold text-xs">
                                                {{ $header }}
                                            </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData as $index => $row)
                                        <tr class="hover:bg-gray-50 {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                            @foreach($row as $cell)
                                            <td class="py-2 px-3 border-b text-gray-800 text-xs whitespace-nowrap">
                                                @if(is_numeric(str_replace([',', '%', 'KSH'], '', $cell)))
                                                @if(str_contains($cell, '%'))
                                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ floatval(str_replace(['%', ','], '', $cell)) >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $cell }}
                                                </span>
                                                @elseif(str_contains($cell, 'KSH'))
                                                <span class="font-medium">{{ $cell }}</span>
                                                @else
                                                <span class="font-medium">{{ $cell }}</span>
                                                @endif
                                                @elseif(in_array(strtolower($cell), ['completed', 'success', 'active', 'in stock']))
                                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800">
                                                    <i class="fas fa-check-circle mr-0.5 text-[10px]"></i> {{ $cell }}
                                                </span>
                                                @elseif(in_array(strtolower($cell), ['pending', 'warning', 'low stock']))
                                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800">
                                                    <i class="fas fa-exclamation-triangle mr-0.5 text-[10px]"></i> {{ $cell }}
                                                </span>
                                                @elseif(in_array(strtolower($cell), ['voided', 'refunded', 'cancelled', 'expired', 'out of stock']))
                                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle mr-0.5 text-[10px]"></i> {{ $cell }}
                                                </span>
                                                @else
                                                {{ $cell }}
                                                @endif
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($totalRecords > 0)
                            <div class="flex-shrink-0 bg-gray-50 px-3 py-2 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-gray-600">
                                        Showing <span class="font-semibold">{{ count($reportData) }}</span> of <span class="font-semibold">{{ $totalRecords }}</span> records
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button class="px-2 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50">
                                            <i class="fas fa-chevron-left text-xs"></i>
                                        </button>
                                        <span class="px-2 py-1 text-xs bg-blue-600 text-white rounded">1</span>
                                        <button class="px-2 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer - Reduced size -->
        <div class="flex-shrink-0 flex flex-wrap h-12 border-t border-gray-300 bg-gradient-to-r from-slate-800 to-slate-900">
            <!-- Windows Icon -->
            <div class="w-12 flex justify-center items-center border-r border-slate-700">
                <button wire:click="toggleMenu"
                    class="text-blue-400 hover:text-blue-300 transition-colors duration-300 hover:scale-110 transform">
                    <i class="fa-brands fa-windows text-lg"></i>
                </button>
            </div>

            <!-- Totals Section -->
            <div class="hidden md:w-32 bg-gradient-to-r from-slate-700 to-slate-800 md:flex justify-center items-center">
                <h3 class="text-white font-bold text-sm">TOTALS</h3>
            </div>

            <!-- Message Section -->
            <div class="flex-1 text-slate-300 flex flex-wrap justify-center items-center gap-3 md:gap-4 px-2 overflow-x-auto py-1">
                <div class="py-0.5 text-center min-w-[80px]">
                    <h3 class="text-[10px] text-slate-400">{{ $summaryTitle ?? 'Records' }}</h3>
                    <p class="text-slate-100 text-sm font-bold">{{ $summaryValue ?? count($reportData) }}</p>
                </div>

                @if(isset($totalAmount))
                <div class="py-0.5 text-center min-w-[80px]">
                    <h3 class="text-[10px] text-slate-400">Total Amount</h3>
                    <p class="text-emerald-300 text-sm font-bold">KSH {{ number_format($totalAmount) }}</p>
                </div>
                @endif

                <div class="py-0.5 text-center min-w-[60px]">
                    <h3 class="text-[10px] text-slate-400">Records</h3>
                    <p class="text-blue-300 text-sm font-bold">{{ $totalRecords }}</p>
                </div>

                <div class="py-0.5 text-center min-w-[100px] hidden md:block">
                    <h3 class="text-[10px] text-slate-400">Date Range</h3>
                    <p class="text-slate-100 text-[10px]">{{ $startDate }} to {{ $endDate }}</p>
                </div>

                @if($selectedBranch && $branches->find($selectedBranch))
                <div class="py-0.5 text-center min-w-[100px] hidden md:block">
                    <h3 class="text-[10px] text-slate-400">Branch</h3>
                    <p class="text-slate-100 text-[10px]">{{ $branches->find($selectedBranch)->name }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add CSS animations -->
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 2px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Modal scrollbar */
        .fixed.inset-0 .flex-col::-webkit-scrollbar {
            width: 4px;
        }

        .fixed.inset-0 .flex-col::-webkit-scrollbar-track {
            background: #2d3748;
            border-radius: 2px;
        }

        .fixed.inset-0 .flex-col::-webkit-scrollbar-thumb {
            background: #4a5568;
            border-radius: 2px;
        }

        .fixed.inset-0 .flex-col::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }

        /* Table header sticky */
        .sticky {
            position: sticky;
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }

            button {
                display: none !important;
            }

            .h-screen {
                height: auto !important;
                border: none !important;
                box-shadow: none !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th,
            td {
                border: 1px solid #000 !important;
                padding: 2px !important;
                font-size: 10px !important;
            }

            .bg-gray-700,
            .bg-gradient-to-r {
                background: #000 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact !important;
            }

            /* Remove scrollbar in print */
            .overflow-auto,
            .overflow-hidden {
                overflow: visible !important;
            }
        }
    </style>
</div>