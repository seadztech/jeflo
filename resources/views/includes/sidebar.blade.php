<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="flex items-center px-6 py-4 m-header h-header-height">
            <a href="../dashboard/index.html" class="flex items-center gap-3 b-brand">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('assets/images/logo-white.svg') }}" class="img-fluid logo logo-lg" alt="logo" />
                <img src="{{ asset('assets/images/favicon.svg') }}" class="img-fluid logo logo-sm" alt="logo" />
            </a>
        </div>
        <div class="navbar-content h-[calc(100vh_-_74px)] py-2.5">
            <ul class="pc-navbar">


                <livewire:components.side-bar-link />

                <div class="ml-2">
                    <livewire:components.header-logout class="pc-link">
                </div>

            </ul>
        </div>
    </div>
</nav>



