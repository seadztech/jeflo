<?php

namespace App\Livewire;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $todaySales;
    public $weeklySales;
    public $yearlySales;
    public $totalProfit;
    public $saleCount;
    public $isLoading = false;
    public $todayProfit;
    public $weeklyProfit;
    public $activeTab = 'statistics'; // Added this property
    
    public $todayPercentage;
    public $weeklyPercentage;
    public $profitPercentage;

    public $routes = [
        ['name'=> 'dashboard', 'icon' => 'house', 'label' => 'Dashboard', 'permission' => null],
        ['name'=> 'roles', 'icon' => 'shield-halved', 'label' => 'Roles', 'permission' => 'view roles'],
        ['name'=> 'branches', 'icon' => 'building', 'label' => 'Branches', 'permission' => 'view branches'],
        ['name'=> 'users', 'icon' => 'users', 'label' => 'Users', 'permission' => 'view users'],
        ['name'=> 'itemTypes', 'icon' => 'list', 'label' => 'Item Types', 'permission' => 'view item types'],
        ['name'=> 'items', 'icon' => 'list', 'label' => 'Items', 'permission' => 'view items'],
        ['name'=> 'sales', 'icon' => 'square-poll-vertical', 'label' => 'Sales', 'permission' => 'view sales'],
        ['name'=> 'transactions', 'icon' => 'money-bill', 'label' => 'Transactions', 'permission' => 'view transactions'],
        ['name'=> 'reports', 'icon' => 'chart-simple', 'label' => 'Reports', 'permission' => 'view reports'],
        ['name'=> 'pos', 'icon' => 'calculator', 'label' => 'POS', 'permission' => 'access pos'],
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->saleCount = Sale::where('status', 'completed')->count();
        $this->loadSalesData();
        $this->calculatePercentages();
    }

    public function loadSalesData()
    {
        // Today's data
        $this->todaySales = Sale::where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount') ?? 0;

        $this->todayProfit = DB::table('sales_items')
            ->join('sales', 'sales_items.sale_id', '=', 'sales.id')
            ->join('items', 'sales_items.item_id', '=', 'items.id')
            ->where('sales.status', 'completed')
            ->whereDate('sales.created_at', Carbon::today())
            ->selectRaw('SUM((sales_items.unit_price - items.buyingPrice) * sales_items.quantity) as profit')
            ->value('profit') ?? 0;

        // Weekly data
        $this->weeklySales = Sale::where('status', 'completed')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total_amount') ?? 0;

        $this->weeklyProfit = DB::table('sales_items')
            ->join('sales', 'sales_items.sale_id', '=', 'sales.id')
            ->join('items', 'sales_items.item_id', '=', 'items.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('SUM((sales_items.unit_price - items.buyingPrice) * sales_items.quantity) as profit')
            ->value('profit') ?? 0;

        // Yearly data
        $this->yearlySales = Sale::where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount') ?? 0;

        $this->totalProfit = DB::table('sales_items')
            ->join('sales', 'sales_items.sale_id', '=', 'sales.id')
            ->join('items', 'sales_items.item_id', '=', 'items.id')
            ->where('sales.status', 'completed')
            ->selectRaw('SUM((sales_items.unit_price - items.buyingPrice) * sales_items.quantity) as profit')
            ->value('profit') ?? 0;
    }

    protected function calculatePercentages()
    {
        $this->todayPercentage = min(100, ($this->todaySales / max(1, $this->weeklySales/7)) * 100);
        $this->weeklyPercentage = min(100, ($this->weeklySales / max(1, $this->yearlySales/52)) * 100);
        $this->profitPercentage = min(100, ($this->totalProfit / max(1, $this->yearlySales)) * 100);
    }

    // Check if user is admin
    public function isAdmin()
    {
        // Adjust this based on your authentication setup
        $user = Auth::user();
        return $user && ($user->hasRole('admin') || $user->is_admin || $user->email === 'admin@example.com');
    }

    // Get filtered routes based on user permissions
    public function getFilteredRoutes()
    {
        $user = Auth::user();
        
        return array_filter($this->routes, function($route) use ($user) {
            if (is_null($route['permission'])) {
                return true;
            }
            
            return $user && $user->can($route['permission']);
        });
    }

    // Prepare chart data for admin view
    public function getDailyChartOptions()
    {
        if ($this->saleCount > 0) {
            $dailySalesBuilder = Sale::query()
                ->select([
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('DATE_FORMAT(created_at, "%b %e") as date_label'),
                    DB::raw('SUM(total_amount) as total'),
                ])
                ->where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->subYear())
                ->groupBy('date', 'date_label')
                ->orderBy('date');

            return [
                'library' => 'chartjs',
                'title' => 'Daily Sales - Last 365 Days',
                'builder' => $dailySalesBuilder,
                'poll' => 60,
                'width' => '100%',
                'height' => '400px',
                'colors' => ['#4e73df'],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true,
                            'title' => [
                                'display' => true,
                                'text' => 'Sales Amount (Ksh)',
                            ],
                        ],
                        'x' => [
                            'title' => [
                                'display' => true,
                                'text' => 'Date',
                            ],
                            'ticks' => [
                                'autoSkip' => true,
                                'maxRotation' => 45,
                                'minRotation' => 45,
                            ],
                        ],
                    ],
                ],
            ];
        }
        
        return null;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'isAdmin' => $this->isAdmin(),
            'filteredRoutes' => $this->getFilteredRoutes(),
            'chartOptions' => $this->getDailyChartOptions()
        ]);
    }
}