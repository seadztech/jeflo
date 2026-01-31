{{-- <livewire:components.header /> --}}

<header class="bg-white shadow-sm app-header">
    <div class="flex items-center justify-between px-4 py-3 header-container sm:px-6">
        <!-- Left side - Menu toggle buttons -->
        <div class="flex items-center">
            <!-- Desktop sidebar toggle -->
            <button id="sidebar-hide"
                class="hidden p-2 text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 lg:inline-flex"
                aria-label="Toggle sidebar">
                <i class="text-lg fas fa-bars"></i>
            </button>

            <!-- Mobile menu toggle -->
            <button id="mobile-collapse"
                class="p-2 text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 lg:hidden"
                aria-label="Toggle mobile menu">
                <i class="text-lg fas fa-bars"></i>
            </button>
        </div>

        <!-- Right side - User controls -->
        <div class="flex items-center space-x-4">
            <!-- Greeting -->
            @php
                $hour = now()->hour;
                $greeting = 'Hello';
                $emoji = '👋';

                if ($hour < 12) {
                    $greeting = 'Good Morning';
                    $emoji = '🌅';
                } elseif ($hour < 17) {
                    $greeting = 'Good Afternoon';
                    $emoji = '☀️';
                } else {
                    $greeting = 'Good Evening';
                    $emoji = '🌙';
                }
            @endphp

            <div class="flex-col hidden text-right sm:flex fade-in">
                <p class="text-sm font-semibold text-gray-700">
                    {{ $greeting }} {{ $emoji }},
                    <span class="text-primary-600">{{ Auth::user()->name }}</span>
                </p>
                <p class="text-xs text-gray-500 ">{{ Auth::user()->email }}</p>
            </div>

            <!-- Theme switcher -->
            <div class="relative">
                <button id="theme-toggle"
                    class="p-2 text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    aria-label="Toggle theme" aria-haspopup="true" aria-expanded="false">
                </button>

                <div id="theme-dropdown"
                    class="absolute right-0 z-10 hidden w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu">
                    <button type="button" onclick="layout_change('dark')"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        role="menuitem">
                        <i class="mr-2 fas fa-moon"></i>
                        <span>Dark Mode</span>
                    </button>
                    <button type="button" onclick="layout_change('light')"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        role="menuitem">
                        <i class="mr-2 fas fa-sun"></i>
                        <span>Light Mode</span>
                    </button>
                    <button type="button" onclick="layout_change_default()"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        role="menuitem">
                        <i class="mr-2 fas fa-cog"></i>
                        <span>System Default</span>
                    </button>
                </div>
            </div>

            <!-- User profile dropdown -->
            <div class="relative">
                <button id="user-menu"
                    class="text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    aria-label="User menu" aria-haspopup="true" aria-expanded="false">
                   <img src="{{ asset('avator.png')}}" alt="User profile" class="w-10 h-10 rounded-full">

                </button>
                

                <div id="user-dropdown"
                    class="absolute right-0 z-10 hidden w-64 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu">
                    <div class="px-4 py-3 border-b">
                        <div class="flex items-center">
                           <img src="{{ asset('avator.png') }}" alt="User profile" class="w-10 h-10 rounded-full">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- @php
                     dd(asset(Auth::user()->avatar));
                    @endphp --}}

                    <div class="py-1">
                        {{-- <button type="button"
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            role="menuitem">
                            <i class="mr-2 text-gray-400 fas fa-cog"></i>
                            <span>Settings</span>
                        </button> --}}
                        {{-- <button type="button"
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            role="menuitem">
                            <i class="mr-2 text-gray-400 fas fa-lock"></i>
                            <span>Change Password</span>
                        </button> --}}
                        <div class="px-4 py-2">
                            <livewire:components.header-logout>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Optional: Animation -->
<style>
    .fade-in {
        animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>


