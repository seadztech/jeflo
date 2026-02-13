<div class="py-6 px-4 sm:px-6  mx-auto">
    <!-- Loading Indicator -->
    @if($isLoading)
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center">
                <i class="fas fa-spinner fa-spin fa-2x text-blue-600 mr-3"></i>
                <span class="text-lg font-medium text-gray-700">Loading dashboard data...</span>
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Dashboard Overview
                    </span>
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Real-time insights and quick access to system modules
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <i class="far fa-clock mr-1.5"></i>
                    {{ now()->format('g:i A') }}
                </span>
                <button wire:click="loadData" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh Data
                </button>
            </div>
        </div>
    </div>

    <!-- Admin View with Tabs -->
    @if($isAdmin)
        <div class="mb-8">
            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-8">
                <nav class="-mb-px flex space-x-8">
                    <button 
                        @class([
                            'group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200',
                            'border-blue-600 text-blue-600' => $activeTab === 'statistics',
                            'border-transparent text-gray-500 hover:text-blue-500 hover:border-blue-300' => $activeTab !== 'statistics'
                        ])
                        wire:click="$set('activeTab', 'statistics')"
                    >
                        <i class="fas fa-chart-bar mr-2"></i>
                        <span class="font-semibold">Statistics</span>
                    </button>
                    <button 
                        @class([
                            'group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200',
                            'border-blue-600 text-blue-600' => $activeTab === 'modules',
                            'border-transparent text-gray-500 hover:text-blue-500 hover:border-blue-300' => $activeTab !== 'modules'
                        ])
                        wire:click="$set('activeTab', 'modules')"
                    >
                        <i class="fas fa-th-large mr-2"></i>
                        <span class="font-semibold">Modules</span>
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            @if($activeTab === 'statistics')
                <!-- Statistics Tab -->
                <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Today Sales -->
                    <div class="bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-600 uppercase tracking-wider">
                                    <i class="fas fa-shopping-bag mr-1"></i>
                                    Today's Sales
                                </p>
                                <h3 class="mt-2 text-3xl font-bold text-gray-900">Ksh {{ number_format($todaySales) }}</h3>
                                <div class="mt-3 flex items-center">
                                    <div class="w-full bg-blue-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" style="width: {{ $todayPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                                <i class="fas fa-receipt text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Today Profit -->
                    <div class="bg-gradient-to-br from-green-50 to-white border border-green-100 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-600 uppercase tracking-wider">
                                    <i class="fas fa-money-bill-wave mr-1"></i>
                                    Today's Profit
                                </p>
                                <h3 class="mt-2 text-3xl font-bold text-gray-900">Ksh {{ number_format($todayProfit) }}</h3>
                                <div class="mt-3 flex items-center">
                                    <div class="w-full bg-green-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: {{ min(100, ($todayProfit/max(1, $todaySales)) * 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white">
                                <i class="fas fa-coins text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Sales -->
                    <div class="bg-gradient-to-br from-purple-50 to-white border border-purple-100 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-purple-600 uppercase tracking-wider">
                                    <i class="fas fa-calendar-week mr-1"></i>
                                    Weekly Sales
                                </p>
                                <h3 class="mt-2 text-3xl font-bold text-gray-900">Ksh {{ number_format($weeklySales) }}</h3>
                                <div class="mt-3 flex items-center">
                                    <div class="w-full bg-purple-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full" style="width: {{ $weeklyPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Profit -->
                    <div class="bg-gradient-to-br from-yellow-50 to-white border border-yellow-100 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-yellow-600 uppercase tracking-wider">
                                    <i class="fas fa-hand-holding-usd mr-1"></i>
                                    Weekly Profit
                                </p>
                                <h3 class="mt-2 text-3xl font-bold text-gray-900">Ksh {{ number_format($weeklyProfit) }}</h3>
                                <div class="mt-3 flex items-center">
                                    <div class="w-full bg-yellow-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-2 rounded-full" style="width: {{ min(100, ($weeklyProfit/max(1, $weeklySales)) * 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white">
                                <i class="fas fa-wallet text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Yearly Sales -->
                    <div class="bg-gradient-to-br from-red-50 to-white border border-red-100 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-red-600 uppercase tracking-wider">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Yearly Sales
                                </p>
                                <h3 class="mt-2 text-3xl font-bold text-gray-900">Ksh {{ number_format($yearlySales) }}</h3>
                                <div class="mt-3 flex items-center">
                                    <div class="w-full bg-red-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-red-500 to-red-600 h-2 rounded-full" style="width: {{ min(100, ($yearlySales/(max(1, $yearlySales))) * 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-red-500 to-red-600 text-white">
                                <i class="fas fa-chart-pie text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Profit -->
                    <div class="bg-gradient-to-br from-indigo-50 to-white border border-indigo-100 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">
                                    <i class="fas fa-piggy-bank mr-1"></i>
                                    Total Profit
                                </p>
                                <h3 class="mt-2 text-3xl font-bold text-gray-900">Ksh {{ number_format($totalProfit) }}</h3>
                                <div class="mt-3 flex items-center">
                                    <div class="w-full bg-indigo-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2 rounded-full" style="width: {{ $profitPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white">
                                <i class="fas fa-trophy text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Chart Section -->
                @if($saleCount > 0 && $chartOptions)
                    <div class="mb-8 bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                            <h3 class="text-lg font-bold text-gray-900">
                                <i class="fas fa-chart-line mr-2"></i>
                                Sales Performance
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">Daily sales trends over the last year</p>
                        </div>
                        <div class="p-4">
                            @livewire('livecharts-bar-chart', $chartOptions)
                        </div>
                    </div>
                @endif

                <!-- Total Sales Count -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Total Completed Sales</h3>
                            <p class="mt-1 text-sm text-gray-600">All-time completed transactions</p>
                        </div>
                        <div class="flex items-center">
                            <div class="text-center mr-6">
                                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                    {{ number_format($saleCount) }}
                                </span>
                                <p class="text-sm text-gray-600 mt-1">Successful transactions</p>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- Modules Tab -->
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">
                        <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            <i class="fas fa-th-large mr-2"></i>
                            System Modules
                        </span>
                    </h3>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach($filteredRoutes as $route)
                            <div class="bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                                <a href="{{ route($route['name']) }}" class="block p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-blue-600 uppercase tracking-wider">
                                                <i class="fas fa-{{ $route['icon'] }} mr-1"></i>
                                                {{ $route['label'] }}
                                            </p>
                                            <p class="mt-2 text-xs text-gray-500">
                                                Click to access {{ $route['label'] }}
                                            </p>
                                        </div>
                                        <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white ml-4">
                                            <i class="fas fa-{{ $route['icon'] }} text-xl"></i>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center text-xs text-gray-500">
                                        <span class="flex-1">Access module</span>
                                        <i class="fas fa-arrow-right text-blue-500"></i>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Non-Admin View (Module Cards Only) -->
        <div class="mb-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        <i class="fas fa-th-large mr-2"></i>
                        Available Modules
                    </span>
                </h2>
                <p class="text-gray-600">Quick access to authorized system features</p>
            </div>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($filteredRoutes as $route)
                    @if($route['name'] !== 'dashboard')
                        <div class="bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                            <a href="{{ route($route['name']) }}" class="block p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-blue-600 uppercase tracking-wider">
                                            <i class="fas fa-{{ $route['icon'] }} mr-1"></i>
                                            {{ $route['label'] }}
                                        </p>
                                        <p class="mt-2 text-xs text-gray-500">
                                            Click to access {{ $route['label'] }}
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white ml-4">
                                        <i class="fas fa-{{ $route['icon'] }} text-xl"></i>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center text-xs text-gray-500">
                                    <span class="flex-1">Access module</span>
                                    <i class="fas fa-arrow-right text-blue-500"></i>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>