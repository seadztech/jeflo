<div class="py-6 px-4 sm:px-6 mx-auto">
    <!-- Loading Indicator -->
    @if($isLoading)
        <div class="text-center py-10">
            <div class="inline-flex items-center justify-center px-4 py-2 rounded border border-gray-200 bg-white shadow-sm">
                <i class="fas fa-spinner fa-spin text-blue-600 mr-3"></i>
                <span class="text-sm font-medium text-gray-600">Loading dashboard data...</span>
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="mb-6 pb-5 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="hidden sm:flex items-center justify-center h-10 w-10 rounded bg-blue-600 text-white flex-shrink-0">
                    <i class="fas fa-tachometer-alt text-lg"></i>
                </span>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900">
                        Dashboard Overview
                    </h1>
                    <p class="mt-0.5 text-sm text-gray-500">
                        Real-time insights and quick access to system modules
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded border border-gray-200 bg-gray-50 text-sm font-medium text-gray-600">
                    <i class="far fa-clock mr-1.5 text-gray-400"></i>
                    {{ now()->format('g:i A') }}
                </span>
                <button wire:click="loadData" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded shadow-sm transition-colors duration-150">
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
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-6">
                    <button
                        @class([
                            'inline-flex items-center py-3 px-1 border-b-2 text-sm font-semibold transition-colors duration-150',
                            'border-blue-600 text-blue-600' => $activeTab === 'statistics',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $activeTab !== 'statistics'
                        ])
                        wire:click="$set('activeTab', 'statistics')"
                    >
                        <i class="fas fa-chart-bar mr-2"></i>
                        Statistics
                    </button>
                    <button
                        @class([
                            'inline-flex items-center py-3 px-1 border-b-2 text-sm font-semibold transition-colors duration-150',
                            'border-blue-600 text-blue-600' => $activeTab === 'modules',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $activeTab !== 'modules'
                        ])
                        wire:click="$set('activeTab', 'modules')"
                    >
                        <i class="fas fa-th-large mr-2"></i>
                        Modules
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            @if($activeTab === 'statistics')
                <!-- Statistics Tab -->
                <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Today Sales -->
                    <div class="relative overflow-hidden rounded shadow-sm bg-blue-600">
                        <div class="relative z-10 p-4">
                            <h3 class="text-3xl font-bold leading-tight text-white">Ksh {{ number_format($todaySales) }}</h3>
                            <p class="mt-1 text-sm font-medium text-blue-100">Today's Sales</p>
                        </div>
                        <i class="fas fa-receipt absolute -right-2 -bottom-4 text-8xl text-white/15"></i>
                        <div class="relative z-10 h-1 bg-black/10">
                            <div class="h-1 bg-white/70" style="width: {{ $todayPercentage }}%"></div>
                        </div>
                    </div>

                    <!-- Today Profit -->
                    <div class="relative overflow-hidden rounded shadow-sm bg-emerald-600">
                        <div class="relative z-10 p-4">
                            <h3 class="text-3xl font-bold leading-tight text-white">Ksh {{ number_format($todayProfit) }}</h3>
                            <p class="mt-1 text-sm font-medium text-emerald-100">Today's Profit</p>
                        </div>
                        <i class="fas fa-coins absolute -right-2 -bottom-4 text-8xl text-white/15"></i>
                        <div class="relative z-10 h-1 bg-black/10">
                            <div class="h-1 bg-white/70" style="width: {{ min(100, ($todayProfit/max(1, $todaySales)) * 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Weekly Sales -->
                    <div class="relative overflow-hidden rounded shadow-sm bg-cyan-600">
                        <div class="relative z-10 p-4">
                            <h3 class="text-3xl font-bold leading-tight text-white">Ksh {{ number_format($weeklySales) }}</h3>
                            <p class="mt-1 text-sm font-medium text-cyan-100">Weekly Sales</p>
                        </div>
                        <i class="fas fa-chart-line absolute -right-2 -bottom-4 text-8xl text-white/15"></i>
                        <div class="relative z-10 h-1 bg-black/10">
                            <div class="h-1 bg-white/70" style="width: {{ $weeklyPercentage }}%"></div>
                        </div>
                    </div>

                    <!-- Weekly Profit -->
                    <div class="relative overflow-hidden rounded shadow-sm bg-amber-400">
                        <div class="relative z-10 p-4">
                            <h3 class="text-3xl font-bold leading-tight text-gray-900">Ksh {{ number_format($weeklyProfit) }}</h3>
                            <p class="mt-1 text-sm font-medium text-amber-900">Weekly Profit</p>
                        </div>
                        <i class="fas fa-wallet absolute -right-2 -bottom-4 text-8xl text-black/10"></i>
                        <div class="relative z-10 h-1 bg-black/10">
                            <div class="h-1 bg-gray-900/60" style="width: {{ min(100, ($weeklyProfit/max(1, $weeklySales)) * 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Yearly Sales -->
                    <div class="relative overflow-hidden rounded shadow-sm bg-rose-600">
                        <div class="relative z-10 p-4">
                            <h3 class="text-3xl font-bold leading-tight text-white">Ksh {{ number_format($yearlySales) }}</h3>
                            <p class="mt-1 text-sm font-medium text-rose-100">Yearly Sales</p>
                        </div>
                        <i class="fas fa-chart-pie absolute -right-2 -bottom-4 text-8xl text-white/15"></i>
                        <div class="relative z-10 h-1 bg-black/10">
                            <div class="h-1 bg-white/70" style="width: {{ min(100, ($yearlySales/(max(1, $yearlySales))) * 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Total Profit -->
                    <div class="relative overflow-hidden rounded shadow-sm bg-indigo-600">
                        <div class="relative z-10 p-4">
                            <h3 class="text-3xl font-bold leading-tight text-white">Ksh {{ number_format($totalProfit) }}</h3>
                            <p class="mt-1 text-sm font-medium text-indigo-100">Total Profit</p>
                        </div>
                        <i class="fas fa-piggy-bank absolute -right-2 -bottom-4 text-8xl text-white/15"></i>
                        <div class="relative z-10 h-1 bg-black/10">
                            <div class="h-1 bg-white/70" style="width: {{ $profitPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Sales Chart Section -->
                @if($saleCount > 0 && $chartOptions)
                    <div class="mb-6 bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-base font-bold text-gray-900">
                                <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                                Sales Performance
                            </h3>
                            <p class="mt-0.5 text-sm text-gray-500">Daily sales trends over the last year</p>
                        </div>
                        <div class="p-4">
                            @livewire('livecharts-bar-chart', $chartOptions)
                        </div>
                    </div>
                @endif

                <!-- Total Sales Count -->
                <div class="bg-white border border-gray-200 border-l-4 border-l-blue-600 rounded shadow-sm p-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Total Completed Sales</h3>
                            <p class="mt-1 text-sm text-gray-500">All-time completed transactions</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center h-12 w-12 rounded bg-blue-600 text-white flex-shrink-0">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-bold text-gray-900">
                                    {{ number_format($saleCount) }}
                                </span>
                                <p class="text-sm text-gray-500 mt-0.5">Successful transactions</p>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- Modules Tab -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-5">
                        <i class="fas fa-th-large mr-2 text-blue-600"></i>
                        System Modules
                    </h3>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach($filteredRoutes as $route)
                            <a href="{{ route($route['name']) }}" class="group block bg-white border border-gray-200 rounded shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-150 p-5">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $route['label'] }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Click to access {{ $route['label'] }}
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-center h-11 w-11 rounded bg-blue-600 text-white ml-3 flex-shrink-0 group-hover:bg-blue-700 transition-colors duration-150">
                                        <i class="fas fa-{{ $route['icon'] }} text-base"></i>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center text-xs font-semibold text-blue-600">
                                    <span class="flex-1">Access module</span>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Non-Admin View (Module Cards Only) -->
        <div class="mb-8">
            <div class="mb-6">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900">
                    <i class="fas fa-th-large mr-2 text-blue-600"></i>
                    Available Modules
                </h2>
                <p class="mt-1 text-sm text-gray-500">Quick access to authorized system features</p>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($filteredRoutes as $route)
                    @if($route['name'] !== 'dashboard')
                        <a href="{{ route($route['name']) }}" class="group block bg-white border border-gray-200 rounded shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-150 p-5">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $route['label'] }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Click to access {{ $route['label'] }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-center h-11 w-11 rounded bg-blue-600 text-white ml-3 flex-shrink-0 group-hover:bg-blue-700 transition-colors duration-150">
                                    <i class="fas fa-{{ $route['icon'] }} text-base"></i>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center text-xs font-semibold text-blue-600">
                                <span class="flex-1">Access module</span>
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>