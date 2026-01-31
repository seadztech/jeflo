
<header class="bg-white shadow-sm dark:bg-black app-header" @click.away="closeAllDropdowns">
    <div class="flex items-center justify-between px-4 py-3 header-container sm:px-6">
        <!-- Left side - Menu toggle buttons -->
        <div class="flex items-center">
            <!-- Desktop sidebar toggle -->
            <button wire:click="$emit('toggleSidebar')"
                class="hidden p-2 text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 lg:inline-flex"
                aria-label="Toggle sidebar">
                <i class="text-lg fas fa-bars"></i>
            </button>

            <!-- Mobile menu toggle -->
            <button wire:click="$emit('toggleMobileMenu')"
                class="p-2 text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 lg:hidden"
                aria-label="Toggle mobile menu">
                <i class="text-lg fas fa-bars"></i>
            </button>
        </div>

        <!-- Right side - User controls -->
        <div class="flex items-center space-x-4">
            <!-- Greeting -->
            @php
                [$greeting, $emoji] = $this->getGreetingProperty();
            @endphp

            <div class="flex-col hidden text-right sm:flex fade-in">
                <p class="text-sm font-semibold text-gray-700 dark:text-white">
                    {{ $greeting }} {{ $emoji }},
                    <span class="text-primary-600">{{ Auth::user()->name }}</span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
            </div>

            <!-- Theme switcher -->
            <div class="relative">
                <button wire:click="toggleThemeDropdown"
                    class="p-2 text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    aria-label="Toggle theme" aria-haspopup="true" :aria-expanded="$showThemeDropdown">
                    <i class="fas {{ $darkMode ? 'fa-moon' : 'fa-sun' }}"></i>
                </button>

                <div x-show="$wire.showThemeDropdown" x-transition
                    class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu" style="display: none;">
                    <button wire:click="toggleTheme('dark')" type="button"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        role="menuitem">
                        <i class="mr-2 fas fa-moon"></i>
                        <span>Dark Mode</span>
                    </button>
                    <button wire:click="toggleTheme('light')" type="button"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        role="menuitem">
                        <i class="mr-2 fas fa-sun"></i>
                        <span>Light Mode</span>
                    </button>
                    <button wire:click="toggleTheme('system')" type="button"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        role="menuitem">
                        <i class="mr-2 fas fa-cog"></i>
                        <span>System Default</span>
                    </button>
                </div>
            </div>

            <!-- User profile dropdown -->
            <div class="relative">
                <button wire:click="toggleUserDropdown"
                    class="text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    aria-label="User menu" aria-haspopup="true" :aria-expanded="$showUserDropdown">
                    <img src="{{ asset(Auth::user()->avatar) }}" alt="User profile" class="w-10 h-10 rounded-full">
                </button>

                <div x-show="$wire.showUserDropdown" x-transition
                    class="absolute right-0 z-10 w-64 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu" style="display: none;">
                    <div class="px-4 py-3 border-b">
                        <div class="flex items-center">
                            <img src="{{ asset(Auth::user()->avatar) }}" alt="User profile" class="w-10 h-10 rounded-full">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="py-1">
                        <a href=""
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            role="menuitem">
                            <i class="mr-2 text-gray-400 fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                        <a href=""
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            role="menuitem">
                            <i class="mr-2 text-gray-400 fas fa-lock"></i>
                            <span>Change Password</span>
                        </a>
                        <div class="px-4 py-2">
                            <livewire:components.header-logout>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function() {
        // Initialize dark mode from session
        if (@json(session('dark_mode', false))) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Listen for theme changes
        Livewire.on('themeChanged', (theme) => {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    });
</script>
@endpush