<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\Sale;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Stockin;
use App\Models\StockChange;
use App\Models\Category;
use App\Models\ItemType;

use App\Models\Allocation;
use App\Models\Categories;
use App\Models\Items;
use App\Models\salesItem;
use App\Models\stockins;
use App\Models\Transactions;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.reports')]
class Reports extends Component
{
    public $isMenuOpen = false;
    public $selectedBranch = '';
    public $selectedReportType = '';
    public $startDate;
    public $endDate;
    public $categoryFilter = '';
    public $itemTypeFilter = '';

    public $reportTitle = '';
    public $reportData = [];
    public $headers = [];
    public $totalRecords = 0;
    public $totalAmount = 0;
    public $summaryTitle = 'Records';
    public $summaryValue = 0;
    public $profitSummary = [];

    public $branches = [];
    public $categories = [];
    public $itemTypes = [];
    public $showAdvancedFilters = false;

    public function mount()
    {
        $this->branches = Branch::all();
        $this->categories = Categories::all();
        $this->itemTypes = ItemType::all();
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function toggleMenu()
    {
        $this->isMenuOpen = !$this->isMenuOpen;
    }

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function generateReport()
    {
        $this->validate([
            'selectedReportType' => 'required',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        $this->reportTitle = match ($this->selectedReportType) {
            'complete_sales' => 'Complete Sales Report',
            'incomplete_sales' => 'Incomplete Sales Report',
            'items' => 'Items Report',
            'transactions' => 'Transactions Report',
            'customers' => 'Customers Report',
            'profit_loss' => 'Profit & Loss Report',
            'stock_levels' => 'Stock Levels Report',
            'top_selling_items' => 'Top Selling Items Report',
            'sales_by_category' => 'Sales by Category Report',
            'payment_method_summary' => 'Payment Method Summary',
            'daily_sales_summary' => 'Daily Sales Summary',
            'customer_purchases' => 'Customer Purchase History',
            'void_refund_summary' => 'Void/Refund Summary',
            'inventory_turnover' => 'Inventory Turnover Report',
            'staff_performance' => 'Staff Performance Report',
            'hourly_sales_analysis' => 'Hourly Sales Analysis',
            'expiring_items' => 'Expiring/Out of Stock Items',

            'loyalty_summary' => 'Customer Loyalty Summary',
            'wastage_report' => 'Wastage/Shrinkage Report',
        };

        $queryParams = [
            'branch_id' => $this->selectedBranch,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'category_id' => $this->categoryFilter,
            'item_type_id' => $this->itemTypeFilter,
        ];

        match ($this->selectedReportType) {
            'complete_sales' => $this->generateSalesReport('completed', $queryParams),
            'incomplete_sales' => $this->generateSalesReport('pending', $queryParams),
            'items' => $this->generateItemsReport($queryParams),
            'transactions' => $this->generateTransactionsReport($queryParams),
            'customers' => $this->generateCustomersReport($queryParams),
            'profit_loss' => $this->generateProfitLossReport($queryParams),
            'stock_levels' => $this->generateStockLevelsReport($queryParams),
            'top_selling_items' => $this->generateTopSellingItemsReport($queryParams),
            'sales_by_category' => $this->generateSalesByCategoryReport($queryParams),
            'payment_method_summary' => $this->generatePaymentMethodSummary($queryParams),
            'daily_sales_summary' => $this->generateDailySalesSummary($queryParams),
            'customer_purchases' => $this->generateCustomerPurchasesReport($queryParams),
            'void_refund_summary' => $this->generateVoidRefundSummary($queryParams),
            // 'inventory_turnover' => $this->generateInventoryTurnoverReport($queryParams),
            'staff_performance' => $this->generateStaffPerformanceReport($queryParams),
            'hourly_sales_analysis' => $this->generateHourlySalesAnalysis($queryParams),
            'expiring_items' => $this->generateExpiringItemsReport($queryParams),
            'sales_tax_summary' => $this->generateSalesTaxSummary($queryParams),
            'loyalty_summary' => $this->generateLoyaltySummary($queryParams),
            'wastage_report' => $this->generateWastageReport($queryParams),
        };

        $this->toggleMenu();
    }

    protected function generateSalesReport($status, $params)
    {
        $startDate = Carbon::parse($params['start_date'])->startOfDay();
        $endDate = Carbon::parse($params['end_date'])->endOfDay();

        $query = Sale::query()
            ->select([
                'sales.id',
                'sales.total_amount',
                'sales.status',
                'sales.payment_method',
                'sales.created_at',
                'customers.name as customer_name',
                DB::raw('COUNT(sales_items.id) as items_count')
            ])
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->leftJoin('sales_items', 'sales.id', '=', 'sales_items.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', $status);

        if (isset($params['branch_id']) && $params['branch_id'] !== null) {
            $query->where('sales.branch_id', $params['branch_id']);
        }

        $sales = $query->groupBy(
            'sales.id',
            'sales.total_amount',
            'sales.status',
            'sales.payment_method',
            'sales.created_at',
            'customers.name'
        )
            ->orderByDesc('sales.created_at')
            ->get();

        $this->headers = [
            'Sales ID',
            'Customer',
            'Date',
            'Items Count',
            'Total',
            'Status',
            'Payment Method'
        ];

        $this->reportData = $sales->map(function ($sale) {
            return [
                $sale->id,
                $sale->customer_name,
                optional($sale->created_at)->format('Y-m-d H:i'),
                $sale->items_count,
                number_format($sale->total_amount, 2),
                $sale->status,
                $sale->payment_method,
            ];
        })->toArray();

        $this->totalRecords = $sales->count();
        $this->totalAmount = $sales->sum('total_amount');
        $this->summaryTitle = 'Total Sales';
        $this->summaryValue = number_format($this->totalAmount, 2);
    }


    protected function generateProfitLossReport($params)
    {
        // Get sales data with cost calculation
        $salesData = Sale::select([
            'sales.id',
            'sales.total_amount',
            DB::raw('SUM(sales_items.quantity * items.buyingPrice) as total_cost'),
            DB::raw('SUM(sales_items.quantity * sales_items.unit_price) as total_revenue')
        ])
            ->join('sales_items', 'sales.id', '=', 'sales_items.sale_id')
            ->join('items', 'sales_items.item_id', '=', 'items.id')
            ->whereBetween('sales.created_at', [$params['start_date'], $params['end_date']])
            ->where('sales.status', 'completed');

        if ($params['branch_id']) {
            $salesData->where('sales.branch_id', $params['branch_id']);
        }

        $salesData = $salesData->groupBy('sales.id', 'sales.total_amount')->first();

        $totalRevenue = $salesData->total_revenue ?? 0;
        $totalCost = $salesData->total_cost ?? 0;

        $grossProfit = $totalRevenue - $totalCost;
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // Calculate other expenses from stock changes (wastage, etc.)
        $expenses = StockChange::whereBetween('created_at', [$params['start_date'], $params['end_date']])
            ->whereIn('reason', ['spoilage', 'wastage'])
            ->sum(DB::raw('quantity * (SELECT buyingPrice FROM items WHERE items.id = (SELECT item_id FROM stockins WHERE stockins.id = stock_changes.stockins_id))'));

        $netProfit = $grossProfit - $expenses;

        $this->headers = ['Metric', 'Amount', 'Percentage'];
        $this->reportData = [
            ['Total Revenue', number_format($totalRevenue, 2), '100%'],
            ['Cost of Goods Sold', number_format($totalCost, 2), $totalRevenue > 0 ? number_format(($totalCost / $totalRevenue) * 100, 2) . '%' : '0%'],
            ['Gross Profit', number_format($grossProfit, 2), number_format($grossMargin, 2) . '%'],
            ['Expenses', number_format($expenses, 2), $totalRevenue > 0 ? number_format(($expenses / $totalRevenue) * 100, 2) . '%' : '0%'],
            ['Net Profit', number_format($netProfit, 2), $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 2) . '%' : '0%'],
        ];

        $this->profitSummary = [
            'revenue' => $totalRevenue,
            'cogs' => $totalCost,
            'gross_profit' => $grossProfit,
            'gross_margin' => $grossMargin,
            'expenses' => $expenses,
            'net_profit' => $netProfit,
        ];

        $this->summaryTitle = 'Net Profit';
        $this->summaryValue = number_format($netProfit, 2);
    }

    protected function generateStockLevelsReport($params)
    {
        // --- Stock ledger subquery (single source of truth) ---
        $stockLedgerSub = DB::table('stock_changes as sc')
            ->join('stockins as si', 'sc.stockins_id', '=', 'si.id')
            ->select(
                'si.item_id',
                DB::raw("
                SUM(
                    CASE
                        WHEN sc.changeType = 'increment'
                        THEN sc.quantity
                        ELSE -sc.quantity
                    END
                ) as current_stock
            ")
            )
            ->when(!empty($params['branch_id']), function ($q) use ($params) {
                $q->where('si.branch_id', $params['branch_id']);
            })
            ->groupBy('si.item_id');

        // --- Main query ---
        $items = Items::select([
            'items.id',
            'items.name',
            'items.unit_price',
            'items.buyingPrice',
            'item_types.name as type_name',
            DB::raw('COALESCE(sl.current_stock, 0) as current_stock'),
        ])
            ->leftJoin('item_types', 'items.item_type_id', '=', 'item_types.id')
            ->leftJoinSub($stockLedgerSub, 'sl', function ($join) {
                $join->on('items.id', '=', 'sl.item_id');
            })
            ->when(!empty($params['item_type_id']), function ($q) use ($params) {
                $q->where('items.item_type_id', $params['item_type_id']);
            })
            ->orderBy('current_stock', 'ASC')
            ->get();

        // --- Report formatting ---
        $this->headers = [
            'ID',
            'Item Name',
            'Type',
            'Current Stock',
            'Unit Price',
            'Stock Value',
            'Status'
        ];

        $reorderLevel = 10;

        $this->reportData = $items->map(function ($item) use ($reorderLevel) {

            $currentStock = (int) $item->current_stock;
            $stockValue = $currentStock * $item->buyingPrice;

            if ($currentStock <= 0) {
                $status = 'Out of Stock';
            } elseif ($currentStock <= $reorderLevel) {
                $status = 'Low Stock';
            } else {
                $status = 'In Stock';
            }

            return [
                'ID' => $item->id,
                'Item Name' => $item->name,
                'Type' => $item->type_name ?? 'N/A',
                'Current Stock' => $currentStock,
                'Unit Price' => number_format($item->unit_price, 2),
                'Stock Value' => number_format($stockValue, 2),
                'Status' => $status,
            ];
        })->values()->toArray();

        $this->totalRecords = count($this->reportData);
        $this->summaryTitle = 'Low Stock Items';

        $this->summaryValue = collect($this->reportData)
            ->where('Status', 'Low Stock')
            ->count();
    }


    protected function generateTopSellingItemsReport($params)
    {
        // Validate required dates
        if (empty($params['start_date']) || empty($params['end_date'])) {
            throw new \InvalidArgumentException('Start date and End date are required.');
        }

        $startDate = \Carbon\Carbon::parse($params['start_date'])->startOfDay();
        $endDate   = \Carbon\Carbon::parse($params['end_date'])->endOfDay();

        $query = DB::table('sales_items')
            ->select([
                'items.id',
                'items.name as item_name',
                DB::raw('COALESCE(SUM(sales_items.quantity), 0) as total_quantity'),
                DB::raw('COALESCE(SUM(sales_items.quantity * sales_items.unit_price), 0) as total_revenue'),
                DB::raw('COALESCE(AVG(sales_items.unit_price), 0) as avg_price'),
                DB::raw('MAX(items.buyingPrice) as buying_price')
            ])
            ->join('items', 'sales_items.item_id', '=', 'items.id')
            ->join('sales', 'sales_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', 'completed');

        // Optional branch filter
        if (!empty($params['branch_id'])) {
            $query->where('sales.branch_id', $params['branch_id']);
        }

        // Optional item type filter
        if (!empty($params['item_type_id'])) {
            $query->where('items.item_type_id', $params['item_type_id']);
        }

        $topItems = $query
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('total_quantity')
            ->limit(50)
            ->get();

        $this->headers = [
            'Rank',
            'Item Name',
            'Quantity Sold',
            'Total Revenue',
            'Average Price',
            'Profit Margin'
        ];

        $this->reportData = [];

        foreach ($topItems as $index => $item) {

            $buyingPrice = $item->buying_price ?? 0;
            $cost = $item->total_quantity * $buyingPrice;
            $revenue = $item->total_revenue ?? 0;

            $margin = $revenue > 0
                ? (($revenue - $cost) / $revenue) * 100
                : 0;

            $this->reportData[] = [
                $index + 1,
                $item->item_name,
                number_format($item->total_quantity),
                number_format($revenue, 2),
                number_format($item->avg_price, 2),
                number_format($margin, 1) . '%',
            ];
        }

        $this->totalRecords = $topItems->count();
        $this->summaryTitle = 'Top Item Revenue';
        $this->summaryValue = $this->totalRecords > 0
            ? number_format($topItems->first()->total_revenue, 2)
            : '0.00';
    }


    protected function generateSalesByCategoryReport($params)
    {
        $startDate = \Carbon\Carbon::parse($params['start_date'])->startOfDay();
        $endDate = \Carbon\Carbon::parse($params['end_date'])->endOfDay();

        $query = salesItem::query()
            ->select([
                'item_types.id',
                'item_types.name as category_name',
                DB::raw('COUNT(DISTINCT sales_items.sale_id) as transaction_count'),
                DB::raw('COALESCE(SUM(sales_items.quantity),0) as total_quantity'),
                DB::raw('COALESCE(SUM(sales_items.quantity * sales_items.unit_price),0) as total_revenue')
            ])
            ->join('items', 'sales_items.item_id', '=', 'items.id')
            ->join('item_types', 'items.item_type_id', '=', 'item_types.id')
            ->join('sales', 'sales_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', 'completed');

        if (isset($params['branch_id']) && $params['branch_id'] !== null) {
            $query->where('sales.branch_id', $params['branch_id']);
        }

        $categories = $query
            ->groupBy('item_types.id', 'item_types.name')
            ->orderByDesc('total_revenue')
            ->get();

        $this->headers = [
            'Category',
            'Transactions',
            'Quantity Sold',
            'Total Revenue',
            'Average Transaction'
        ];

        $totalRevenue = $categories->sum('total_revenue');

        $this->reportData = $categories->map(function ($category) use ($totalRevenue) {

            $avgTransaction = $category->transaction_count > 0
                ? $category->total_revenue / $category->transaction_count
                : 0;

            return [
                $category->category_name,
                $category->transaction_count,
                number_format($category->total_quantity),
                number_format($category->total_revenue, 2),
                number_format($avgTransaction, 2),
            ];
        })->toArray();

        $this->totalRecords = $categories->count();
        $this->summaryTitle = 'Total Revenue by Category';
        $this->summaryValue = number_format($totalRevenue, 2);
    }


    protected function generateDailySalesSummary($params)
    {
        $startDate = \Carbon\Carbon::parse($params['start_date'])->startOfDay();
        $endDate = \Carbon\Carbon::parse($params['end_date'])->endOfDay();

        $query = Sale::query()
            ->select([
                DB::raw('DATE(created_at) as sale_date'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('COALESCE(SUM(total_amount),0) as total_revenue'),
                DB::raw('COALESCE(AVG(total_amount),0) as avg_transaction'),
                DB::raw('COUNT(DISTINCT customer_id) as unique_customers')
            ])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed');

        if (isset($params['branch_id']) && $params['branch_id'] !== null) {
            $query->where('branch_id', $params['branch_id']);
        }

        $dailySales = $query
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc('sale_date')
            ->get();

        $this->headers = [
            'Date',
            'Transactions',
            'Total Revenue',
            'Average Transaction',
            'Unique Customers',
            'Day of Week'
        ];

        $this->reportData = $dailySales->map(function ($day) {
            return [
                $day->sale_date,
                $day->transaction_count,
                number_format($day->total_revenue, 2),
                number_format($day->avg_transaction, 2),
                $day->unique_customers,
                \Carbon\Carbon::parse($day->sale_date)->format('l'),
            ];
        })->toArray();

        $this->totalRecords = $dailySales->count();

        $this->summaryTitle = 'Average Daily Revenue';

        $this->summaryValue = $this->totalRecords > 0
            ? number_format($dailySales->sum('total_revenue') / $this->totalRecords, 2)
            : '0.00';
    }


    protected function generateCustomerPurchasesReport($params)
    {
        $customers = Customer::select([
            'customers.id',
            'customers.name',
            'customers.phone_number',
            'customers.email',
            DB::raw('COUNT(sales.id) as total_visits'),
            DB::raw('SUM(sales.total_amount) as total_spent'),
            DB::raw('MAX(sales.created_at) as last_visit')
        ])
            ->leftJoin('sales', function ($join) use ($params) {
                $join->on('customers.id', '=', 'sales.customer_id')
                    ->whereBetween('sales.created_at', [$params['start_date'], $params['end_date']])
                    ->where('sales.status', 'completed');
                if ($params['branch_id']) {
                    $join->where('sales.branch_id', $params['branch_id']);
                }
            })
            ->groupBy('customers.id', 'customers.name', 'customers.phone_number', 'customers.email')
            ->havingRaw('COUNT(sales.id) > 0')
            ->orderBy('total_spent', 'DESC')
            ->get();

        $this->headers = ['Customer ID', 'Name', 'Phone', 'Total Visits', 'Total Spent', 'Average Spend', 'Last Visit'];

        $this->reportData = $customers->map(function ($customer) {
            $avgSpend = $customer->total_visits > 0 ?
                $customer->total_spent / $customer->total_visits : 0;

            return [
                $customer->id,
                $customer->name,
                $customer->phone_number,
                $customer->total_visits,
                number_format($customer->total_spent, 2),
                number_format($avgSpend, 2),
                $customer->last_visit ? Carbon::parse($customer->last_visit)->format('Y-m-d H:i') : 'N/A',
            ];
        })->toArray();

        $this->totalRecords = $customers->count();
        $this->summaryTitle = 'Top Customer Spending';
        $this->summaryValue = count($this->reportData) > 0 ?
            number_format($this->reportData[0][4] ?? 0, 2) : '0.00';
    }

    protected function generateStaffPerformanceReport($params)
    {
        $staffPerformance = Sale::select([
            'sales.actionBy',
            DB::raw('users.name as staff_name'),
            DB::raw('COUNT(sales.id) as transaction_count'),
            DB::raw('SUM(sales.total_amount) as total_revenue'),
            DB::raw('AVG(sales.total_amount) as avg_sale')
        ])
            ->leftJoin('users', 'sales.actionBy', '=', 'users.id')
            ->whereBetween('sales.created_at', [$params['start_date'], $params['end_date']])
            ->where('sales.status', 'completed');

        if ($params['branch_id']) {
            $staffPerformance->where('sales.branch_id', $params['branch_id']);
        }

        $staffPerformance = $staffPerformance->groupBy('sales.actionBy', 'users.name')
            ->orderBy('total_revenue', 'DESC')
            ->get();

        $this->headers = ['Staff ID', 'Staff Name', 'Transactions', 'Total Revenue', 'Average Sale', 'Commission'];

        $this->reportData = $staffPerformance->map(function ($staff) {
            $commission = $staff->total_revenue * 0.02; // 2% commission

            return [
                $staff->actionBy,
                $staff->staff_name ?? 'Unknown',
                $staff->transaction_count,
                number_format($staff->total_revenue, 2),
                number_format($staff->avg_sale, 2),
                number_format($commission, 2),
            ];
        })->toArray();

        $this->totalRecords = count($this->reportData);
        $this->summaryTitle = 'Top Performer';
        $this->summaryValue = count($this->reportData) > 0 ?
            $this->reportData[0][1] . ' - ' . $this->reportData[0][3] : 'N/A';
    }

    protected function generateHourlySalesAnalysis($params)
    {
        $hourlyData = Sale::select([
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as transactions'),
            DB::raw('SUM(total_amount) as revenue'),
            DB::raw('AVG(total_amount) as avg_sale')
        ])
            ->whereBetween('created_at', [$params['start_date'], $params['end_date']])
            ->where('status', 'completed');

        if ($params['branch_id']) {
            $hourlyData->where('branch_id', $params['branch_id']);
        }

        $hourlyData = $hourlyData->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Fill missing hours
        $fullHourlyData = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourRecord = $hourlyData->firstWhere('hour', $hour);
            $fullHourlyData[$hour] = [
                'transactions' => $hourRecord->transactions ?? 0,
                'revenue' => $hourRecord->revenue ?? 0,
                'avg_sale' => $hourRecord->avg_sale ?? 0,
            ];
        }

        $this->headers = ['Hour', 'Transactions', 'Total Revenue', 'Average Sale', 'Peak Indicator'];

        $maxTransactions = max(array_column($fullHourlyData, 'transactions'));

        $this->reportData = [];
        foreach ($fullHourlyData as $hour => $data) {
            $peakIndicator = '';
            if ($maxTransactions > 0) {
                $percentage = ($data['transactions'] / $maxTransactions) * 100;
                if ($percentage > 80) {
                    $peakIndicator = '📈 Peak';
                } elseif ($percentage > 50) {
                    $peakIndicator = '↗️ Busy';
                } elseif ($percentage > 20) {
                    $peakIndicator = '➡️ Moderate';
                } else {
                    $peakIndicator = '↘️ Quiet';
                }
            }

            $this->reportData[] = [
                sprintf('%02d:00 - %02d:59', $hour, $hour),
                $data['transactions'],
                number_format($data['revenue'], 2),
                number_format($data['avg_sale'], 2),
                $peakIndicator,
            ];
        }

        $this->totalRecords = 24;
        $this->summaryTitle = 'Peak Hour';
        $this->summaryValue = array_search($maxTransactions, array_column($fullHourlyData, 'transactions')) . ':00';
    }

    protected function generatePaymentMethodSummary($params)
    {
        $paymentMethods = Sale::select([
            'payment_method',
            DB::raw('COUNT(*) as transaction_count'),
            DB::raw('SUM(total_amount) as total_amount'),
            DB::raw('AVG(total_amount) as avg_transaction')
        ])
            ->whereBetween('created_at', [$params['start_date'], $params['end_date']])
            ->where('status', 'completed');

        // if ($params['branch_id']) {
        //     $paymentMethods->where('branch_id', $params['branch_id']);
        // }

        $paymentMethods = $paymentMethods->groupBy('payment_method')
            ->orderBy('total_amount', 'DESC')
            ->get();

        $this->headers = ['Payment Method', 'Transactions', 'Total Amount', 'Average Transaction', 'Percentage'];

        $totalAmount = $paymentMethods->sum('total_amount');

        $this->reportData = $paymentMethods->map(function ($method) use ($totalAmount) {
            $percentage = $totalAmount > 0 ? ($method->total_amount / $totalAmount) * 100 : 0;

            return [
                $method->payment_method ?? 'Cash',
                $method->transaction_count,
                number_format($method->total_amount, 2),
                number_format($method->avg_transaction, 2),
                number_format($percentage, 1) . '%',
            ];
        })->toArray();

        $this->totalRecords = $paymentMethods->count();
        $this->summaryTitle = 'Most Used Payment Method';
        $this->totalAmount = $totalAmount;
        $this->summaryValue = count($this->reportData) > 0 ? $this->reportData[0][0] : 'N/A';
    }

    protected function generateVoidRefundSummary($params)
    {
        $voidRefunds = Sale::whereBetween('created_at', [$params['start_date'], $params['end_date']])
            ->whereIn('status', ['voided', 'refunded']);

        if ($params['branch_id']) {
            $voidRefunds->where('branch_id', $params['branch_id']);
        }

        $voidRefunds = $voidRefunds->get();

        $this->headers = ['Transaction ID', 'Date', 'Original Amount', 'Type', 'Processed By'];

        $this->reportData = $voidRefunds->map(function ($transaction) {
            return [
                $transaction->id,
                $transaction->created_at->format('Y-m-d H:i'),
                number_format($transaction->total_amount, 2),
                ucfirst($transaction->status),
                $transaction->user->name ?? 'Unknown',
            ];
        })->toArray();

        $this->totalRecords = $voidRefunds->count();
        $this->totalAmount = $voidRefunds->sum('total_amount');
        $this->summaryTitle = 'Total Void/Refund Amount';
        $this->summaryValue = number_format($this->totalAmount, 2);
    }

    // protected function generateInventoryTurnoverReport($params)
    // {
    //     // Get items with stock movement
    //     $items = Items::select([
    //             'items.id',
    //             'items.name',

    //             'item_types.name as type_name',
    //             DB::raw('COALESCE(SUM(CASE WHEN stockins.created_at < ? THEN stockins.quantity ELSE 0 END), 0) as beginning_stock'),
    //             DB::raw('COALESCE(SUM(CASE WHEN stockins.created_at BETWEEN ? AND ? THEN stockins.quantity ELSE 0 END), 0) as stock_in'),
    //             DB::raw('COALESCE(SUM(CASE WHEN sales.created_at BETWEEN ? AND ? AND sales.status = "completed" THEN sales_items.quantity ELSE 0 END), 0) as stock_out')
    //         ])
    //         // ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
    //         ->leftJoin('item_types', 'items.item_type_id', '=', 'item_types.id')
    //         ->leftJoin('stockins', function($join) use ($params) {
    //             $join->on('items.id', '=', 'stockins.item_id');
    //             if ($params['branch_id']) {
    //                 $join->where('stockins.branch_id', $params['branch_id']);
    //             }
    //         })
    //         ->leftJoin('sales_items', 'items.id', '=', 'sales_items.item_id')
    //         ->leftJoin('sales', function($join) use ($params) {
    //             $join->on('sales_items.sale_id', '=', 'sales.id')
    //                 ->where('sales.status', 'completed');
    //             if ($params['branch_id']) {
    //                 $join->where('sales.branch_id', $params['branch_id']);
    //             }
    //         })
    //         ->setBindings([
    //             $params['start_date'], // beginning stock
    //             $params['start_date'], $params['end_date'], // stock_in
    //             $params['start_date'], $params['end_date']  // stock_out
    //         ])
    //         ->groupBy('items.id', 'items.name', 'item_types.name')
    //         ->get();

    //     $this->headers = ['Item', 'Type', 'Beginning Stock', 'Stock In', 'Stock Out', 'Ending Stock', 'Turnover Rate'];

    //     $this->reportData = $items->map(function ($item) use ($params) {
    //         $beginningStock = $item->beginning_stock;
    //         $stockIn = $item->stock_in;
    //         $stockOut = $item->stock_out;
    //         $endingStock = $beginningStock + $stockIn - $stockOut;

    //         // Calculate turnover rate
    //         $avgStock = ($beginningStock + $endingStock) / 2;
    //         $turnoverRate = $avgStock > 0 ? ($stockOut / $avgStock) : 0;

    //         // Annualize the rate
    //         $daysInPeriod = Carbon::parse($params['end_date'])->diffInDays($params['start_date']) + 1;
    //         $annualizedTurnover = $turnoverRate * (365 / $daysInPeriod);

    //         $efficiency = 'Low';
    //         if ($annualizedTurnover >= 12) $efficiency = 'Very High';
    //         elseif ($annualizedTurnover >= 8) $efficiency = 'High';
    //         elseif ($annualizedTurnover >= 4) $efficiency = 'Good';
    //         elseif ($annualizedTurnover >= 2) $efficiency = 'Moderate';

    //         return [
    //             $item->name,

    //             $item->type_name ?? 'N/A',
    //             number_format($beginningStock),
    //             number_format($stockIn),
    //             number_format($stockOut),
    //             number_format($endingStock),
    //             number_format($annualizedTurnover, 1) . 'x (' . $efficiency . ')',
    //         ];
    //     })->sortByDesc(function ($item) {
    //         // return floatval(explode('x', $item[7])[0]);
    //     })->values()->toArray();

    //     $this->totalRecords = count($this->reportData);

    //     // Calculate average turnover
    //     $totalTurnover = collect($this->reportData)->sum(function ($item) {
    //         return floatval(explode('x', $item[7])[0]);
    //     });

    //     $this->summaryTitle = 'Avg Annual Turnover';
    //     $this->summaryValue = $this->totalRecords > 0 ? 
    //         number_format($totalTurnover / $this->totalRecords, 1) . 'x' : '0x';
    // }

    protected function generateExpiringItemsReport($params)
    {
        $expiringItems = stockins::select([
            'items.name',
            'stockins.batch_id',
            'stockins.expiry_date',
            'stockins.quantity as current_quantity',
            DB::raw('DATEDIFF(stockins.expiry_date, CURDATE()) as days_until_expiry')
        ])
            ->join('items', 'stockins.item_id', '=', 'items.id')
            ->where('stockins.expiry_date', '<=', now()->addDays(30))
            ->where('stockins.expiry_date', '>=', now())
            ->where('stockins.quantity', '>', 0);

        if ($params['branch_id']) {
            $expiringItems->where('stockins.branch_id', $params['branch_id']);
        }

        $expiringItems = $expiringItems->orderBy('stockins.expiry_date')
            ->get();

        $this->headers = ['Item', 'Batch Number', 'Expiry Date', 'Days Until Expiry', 'Current Stock', 'Status'];

        $this->reportData = $expiringItems->map(function ($stock) {
            $daysUntilExpiry = $stock->days_until_expiry;

            $status = 'OK';
            if ($daysUntilExpiry <= 0) {
                $status = '⚠️ Expired';
            } elseif ($daysUntilExpiry <= 7) {
                $status = '🔴 Urgent';
            } elseif ($daysUntilExpiry <= 30) {
                $status = '🟡 Warning';
            }

            return [
                $stock->name,
                $stock->batch_id ?? 'N/A',
                $stock->expiry_date,
                $daysUntilExpiry > 0 ? $daysUntilExpiry : 'Expired',
                $stock->current_quantity,
                $status,
            ];
        })->toArray();

        $this->totalRecords = count($this->reportData);
        $this->summaryTitle = 'Expiring Soon (≤7 days)';

        // FIX: Changed from index 6 to 5 since we removed the category column
        $this->summaryValue = collect($this->reportData)->filter(function ($item) {
            return strpos($item[5], 'Urgent') !== false; // Changed from [6] to [5]
        })->count();
    }

    protected function generateSalesTaxSummary($params)
    {
        // Note: Your schema doesn't have tax_amount field. You might need to add it.
        // For now, I'll assume tax is calculated as 16% of total
        $taxData = Sale::select([
            DB::raw('DATE(created_at) as sale_date'),
            DB::raw('SUM(total_amount) as total_sales'),
            DB::raw('SUM(total_amount) / 1.16 as net_sales'), // Assuming 16% VAT
            DB::raw('SUM(total_amount) - (SUM(total_amount) / 1.16) as total_tax')
        ])
            ->whereBetween('created_at', [$params['start_date'], $params['end_date']])
            ->where('status', 'completed');

        if ($params['branch_id']) {
            $taxData->where('branch_id', $params['branch_id']);
        }

        $taxData = $taxData->groupBy('sale_date')
            ->orderBy('sale_date', 'DESC')
            ->get();

        $this->headers = ['Date', 'Total Sales', 'Net Sales', 'Tax Amount', 'Tax Rate'];

        $this->reportData = $taxData->map(function ($data) {
            $taxRate = $data->net_sales > 0 ? ($data->total_tax / $data->net_sales) * 100 : 0;

            return [
                $data->sale_date,
                number_format($data->total_sales, 2),
                number_format($data->net_sales, 2),
                number_format($data->total_tax, 2),
                number_format($taxRate, 2) . '%',
            ];
        })->toArray();

        $this->totalRecords = $taxData->count();
        $this->summaryTitle = 'Total Tax Collected';
        $this->summaryValue = number_format($taxData->sum('total_tax'), 2);
    }

    protected function generateLoyaltySummary($params)
    {
        $customers = Customer::select([
            'customers.id',
            'customers.name',
            DB::raw('COUNT(sales.id) as total_visits'),
            DB::raw('SUM(sales.total_amount) as total_spent'),
            DB::raw('MAX(sales.created_at) as last_visit')
        ])
            ->leftJoin('sales', function ($join) use ($params) {
                $join->on('customers.id', '=', 'sales.customer_id')
                    ->whereBetween('sales.created_at', [$params['start_date'], $params['end_date']])
                    ->where('sales.status', 'completed');
                if ($params['branch_id']) {
                    $join->where('sales.branch_id', $params['branch_id']);
                }
            })
            ->groupBy('customers.id', 'customers.name')
            ->orderBy('total_spent', 'DESC')
            ->get();

        $loyaltyTiers = [
            'Platinum' => ['visits' => 20, 'spend' => 10000],
            'Gold' => ['visits' => 10, 'spend' => 5000],
            'Silver' => ['visits' => 5, 'spend' => 2000],
            'Bronze' => ['visits' => 2, 'spend' => 500],
            'New' => ['visits' => 1, 'spend' => 0],
        ];

        $this->headers = ['Customer', 'Total Visits', 'Total Spend', 'Average Visit', 'Last Visit', 'Loyalty Tier'];

        $this->reportData = $customers->map(function ($customer) use ($loyaltyTiers) {
            $totalSpent = $customer->total_spent ?? 0;
            $visitCount = $customer->total_visits ?? 0;
            $avgVisit = $visitCount > 0 ? $totalSpent / $visitCount : 0;

            // Determine loyalty tier
            $tier = 'New';
            foreach ($loyaltyTiers as $tierName => $requirements) {
                if ($visitCount >= $requirements['visits'] && $totalSpent >= $requirements['spend']) {
                    $tier = $tierName;
                    break;
                }
            }

            return [
                $customer->name,
                $visitCount,
                number_format($totalSpent, 2),
                number_format($avgVisit, 2),
                $customer->last_visit ? Carbon::parse($customer->last_visit)->format('Y-m-d') : 'N/A',
                $tier,
            ];
        })->toArray();

        $this->totalRecords = $customers->count();

        // Count customers by tier
        $tierCounts = [
            'Platinum' => 0,
            'Gold' => 0,
            'Silver' => 0,
            'Bronze' => 0,
            'New' => 0,
        ];

        foreach ($this->reportData as $customer) {
            $tier = $customer[5];
            if (isset($tierCounts[$tier])) {
                $tierCounts[$tier]++;
            }
        }

        $this->summaryTitle = 'Loyalty Distribution';
        $this->summaryValue = implode(', ', array_map(function ($tier, $count) {
            return "$tier: $count";
        }, array_keys($tierCounts), $tierCounts));
    }

    protected function generateWastageReport($params)
    {
        $wastages = StockChange::select([
            'stock_changes.created_at',
            'items.name as item_name',
            'stockins.batch_id',
            'stock_changes.quantity',
            'stock_changes.reason',
            DB::raw('ABS(stock_changes.quantity * items.buyingPrice) as cost'),
            'users.name as reported_by'
        ])
            ->join('stockins', 'stock_changes.stockins_id', '=', 'stockins.id')
            ->join('items', 'stockins.item_id', '=', 'items.id')
            ->leftJoin('users', 'stock_changes.actionBy', '=', 'users.id')
            ->whereBetween('stock_changes.created_at', [$params['start_date'], $params['end_date']])
            ->whereIn('stock_changes.reason', ['spoilage', 'wastage'])
            ->orderBy('stock_changes.created_at', 'DESC')
            ->get();

        $this->headers = ['Date', 'Item', 'Batch', 'Quantity', 'Reason', 'Cost', 'Reported By'];

        $this->reportData = $wastages->map(function ($wastage) {
            return [
                $wastage->created_at->format('Y-m-d H:i'),
                $wastage->item_name,
                $wastage->batch_id ?? 'N/A',
                abs($wastage->quantity),
                $wastage->reason ?? 'N/A',
                number_format($wastage->cost, 2),
                $wastage->reported_by ?? 'Unknown',
            ];
        })->toArray();

        $this->totalRecords = $wastages->count();
        $this->summaryTitle = 'Total Wastage Cost';
        $this->summaryValue = number_format($wastages->sum('cost'), 2);
    }

    protected function generateItemsReport($params)
    {
        // --- Stock ledger subquery (true stock calculation) ---
        $stockLedgerSub = DB::table('stock_changes as sc')
            ->join('stockins as si', 'sc.stockins_id', '=', 'si.id')
            ->select(
                'si.item_id',
                DB::raw("
                SUM(
                    CASE
                        WHEN sc.changeType = 'increment'
                        THEN sc.quantity
                        ELSE -sc.quantity
                    END
                ) as current_stock
            ")
            )
            ->groupBy('si.item_id');

        // --- Main query ---
        $query = Items::select([
            'items.id',
            'items.name',
            'items.unit_price',
            'items.buyingPrice',
            'item_types.name as type_name',
            DB::raw('COALESCE(sl.current_stock, 0) as current_stock'),
            'items.created_at'
        ])
            ->leftJoin('item_types', 'items.item_type_id', '=', 'item_types.id')
            ->leftJoinSub($stockLedgerSub, 'sl', function ($join) {
                $join->on('items.id', '=', 'sl.item_id');
            });

        // --- Filters ---
        if (!empty($params['category_id'])) {
            $query->where('items.category_id', $params['category_id']);
        }

        if (!empty($params['item_type_id'])) {
            $query->where('items.item_type_id', $params['item_type_id']);
        }

        $items = $query
            ->orderBy('items.created_at', 'DESC')
            ->get();

        // --- Report headers ---
        $this->headers = [
            'ID',
            'Name',
            'Type',
            'Selling Price',
            'Cost Price',
            'Margin',
            'Current Stock',
            'Stock Value',
            'Date Created'
        ];

        // --- Format report ---
        $this->reportData = $items->map(function ($item) {

            $costPrice = $item->buyingPrice ?? 0;

            $margin = $costPrice > 0
                ? (($item->unit_price - $costPrice) / $costPrice) * 100
                : 0;

            $currentStock = (int) $item->current_stock;
            $stockValue = $currentStock * $costPrice;

            return [
                $item->id,
                $item->name,
                $item->type_name ?? 'N/A',
                number_format($item->unit_price, 2),
                number_format($costPrice, 2),
                number_format($margin, 1) . '%',
                $currentStock,
                number_format($stockValue, 2),
                $item->created_at->format('Y-m-d'),
            ];
        })->toArray();

        // --- Summary ---
        $this->totalRecords = $items->count();
        $this->summaryTitle = 'Average Margin';

        $totalMargin = collect($this->reportData)->sum(function ($item) {
            return floatval(str_replace('%', '', $item[5]));
        });

        $this->summaryValue = $this->totalRecords > 0
            ? number_format($totalMargin / $this->totalRecords, 1) . '%'
            : '0%';
    }


    protected function generateTransactionsReport($params)
    {
        $startDate = Carbon::parse($params['start_date'])->startOfDay();
        $endDate = Carbon::parse($params['end_date'])->endOfDay();

        $query = Transactions::query()
            ->select([
                'transactions.id',
                'transactions.type',
                'transactions.transaction_code',
                'transactions.amount',
                'transactions.created_at',
                'transactions.sale_id',
                'customers.name as customer_name',
                'sales.status',
                'sales.payment_method'
            ])
            ->leftJoin('sales', 'transactions.sale_id', '=', 'sales.id')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate]);

        // Branch filter: only filter if branch_id is set and not "all"
        if (isset($params['branch_id']) && $params['branch_id'] !== 'all' && !empty($params['branch_id'])) {
            $branchId = $params['branch_id'];
            $query->where(function ($q) use ($branchId) {
                $q->whereNull('transactions.sale_id') // standalone transactions
                    ->orWhere('sales.branch_id', $branchId); // linked to sales in branch
            });
        }

        $transactions = $query->orderByDesc('transactions.created_at')->get();

        $this->headers = [
            'ID',
            'Type',
            'Transaction Code',
            'Amount',
            'Date',
            'Customer',
            'Sale ID',
            'Status',
            'Payment Method'
        ];

        $this->reportData = $transactions->map(function ($transaction) {
            return [
                $transaction->id,
                $transaction->type,
                $transaction->transaction_code,
                number_format($transaction->amount, 2),
                optional($transaction->created_at)->format('Y-m-d H:i'),
                $transaction->customer_name ?? 'Walk-in',
                $transaction->sale_id ?? 'N/A',
                $transaction->status ?? 'completed',
                $transaction->payment_method ?? 'Cash',
            ];
        })->toArray();

        $this->totalRecords = $transactions->count();
        $this->totalAmount = $transactions->sum('amount');
        $this->summaryTitle = 'Total Transactions';
        $this->summaryValue = number_format($this->totalAmount, 2);
    }



    protected function generateCustomersReport($params)
    {
        $customers = Customer::select([
            'customers.id',
            'customers.name',
            'customers.phone_number',
            'customers.email',
            DB::raw('COUNT(sales.id) as total_sales'),
            DB::raw('SUM(sales.total_amount) as total_spent'),
            DB::raw('MAX(sales.created_at) as last_purchase'),
            'customers.created_at'
        ])
            ->leftJoin('sales', function ($join) use ($params) {
                $join->on('customers.id', '=', 'sales.customer_id')
                    ->where('sales.status', 'completed');
                if ($params['branch_id']) {
                    $join->where('sales.branch_id', $params['branch_id']);
                }
            })
            ->groupBy(
                'customers.id',
                'customers.name',
                'customers.phone_number',
                'customers.email',
                'customers.created_at'
            )
            ->orderBy('total_spent', 'DESC')
            ->get();

        $this->headers = ['ID', 'Name', 'Phone', 'Email', 'Total Purchases', 'Total Spent', 'Last Purchase', 'Customer Since'];

        $this->reportData = $customers->map(function ($customer) {
            return [
                $customer->id,
                $customer->name,
                $customer->phone_number,
                $customer->email ?? 'N/A',
                $customer->total_sales,
                number_format($customer->total_spent ?? 0, 2),
                $customer->last_purchase ? Carbon::parse($customer->last_purchase)->format('Y-m-d') : 'Never',
                $customer->created_at->format('Y-m-d'),
            ];
        })->toArray();

        $this->totalRecords = $customers->count();
        $this->summaryTitle = 'Top Customer Total';
        $this->summaryValue = count($this->reportData) > 0 ?
            $this->reportData[0][5] : '0.00';
    }

    // Export methods
    public function exportToExcel()
    {
        if (empty($this->reportData)) {
            return;
        }

        $this->dispatch('export-to-excel', [
            'title' => $this->reportTitle,
            'headers' => $this->headers,
            'data' => $this->reportData,
            'summary' => [
                'title' => $this->summaryTitle,
                'value' => $this->summaryValue,
                'totalRecords' => $this->totalRecords,
                'totalAmount' => $this->totalAmount ?? null,
            ]
        ]);
    }

    public function exportToPDF()
    {
        if (empty($this->reportData)) {
            return;
        }

        $pdf = PDF::loadView('exports.report-pdf', [
            'title' => $this->reportTitle,
            'headers' => $this->headers,
            'data' => $this->reportData,
            'totalRecords' => $this->totalRecords,
            'totalAmount' => $this->totalAmount ?? null,
            'summaryTitle' => $this->summaryTitle,
            'summaryValue' => $this->summaryValue,
            'profitSummary' => $this->profitSummary ?? null,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $this->reportTitle . '.pdf');
    }

    // Quick report generation
    public function generateQuickReport($reportType, $days = 7)
    {
        $this->startDate = now()->subDays($days)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->selectedReportType = $reportType;
        $this->generateReport();
    }

    public function resetFilters()
    {
        $this->reset(['selectedBranch', 'categoryFilter', 'itemTypeFilter']);
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function dashboard()
    {
        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.reports.reports');
    }
}
