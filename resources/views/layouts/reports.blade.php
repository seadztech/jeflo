<!doctype html>
<html lang="en" data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-direction="ltr" dir="ltr"
    data-pc-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Care Pharmacy</title>

    <!-- Meta Information -->
    <meta name="description" content="Care Pharmacy Management System">
    <meta name="keywords" content="Pharmacy, Management, System">
    <meta name="author" content="SeadzTech">
    

    <!-- ================== PWA CONFIGURATION ================== -->
    <meta name="theme-color" content="#6777ef" />
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Care Pharmacy">
    <meta name="msapplication-TileImage" content="{{ asset('icons/icon-144x144.png') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Care Pharmacy">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('icons/icon-32x32.png') }}" type="image/png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
    <script src="https://kit.fontawesome.com/8e5d576196.js" crossorigin="anonymous"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
    <base href="{{ url('/') }}/">

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white">
    <!-- [ Sidebar Menu ] start -->
    {{-- @include('includes.sidebar') --}}
    <!-- [ Sidebar Menu ] end -->

    <!-- [ Header Topbar ] start -->
    @include('includes.header')
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="w-full max-h-screen">
        {{ $slot }}
       <div class="w-full text-right right-2">
         <p class="inline-block text-purple-500 max-sm:mr-3 sm:ml-2 uppercase border p-1 font-thin">
            <a class="" href="https://seadztech.co.ke" target="_blank"> Powered By SeadzTech</a>
        </p>
       </div>
    </div>
    <!-- [ Main Content ] end -->



    <!-- ================== SCRIPTS ================== -->
    <!-- Vendor JS -->
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/icon/custom-icon.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <!-- Layout Configuration -->
    <script>
        layout_change('false');
        layout_theme_sidebar_change('dark');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');
        main_layout_change('vertical');
    </script>

    <!-- UI Interaction Scripts -->
    <script>
        document.addEventListener('livewire:navigated', function() {
            // Theme Dropdown
            const themeToggle = document.getElementById('theme-toggle');
            const themeDropdown = document.getElementById('theme-dropdown');

            // User Dropdown
            const userMenu = document.getElementById('user-menu');
            const userDropdown = document.getElementById('user-dropdown');

            // Mobile Menu
            const mobileToggleBtn = document.getElementById('mobile-collapse');
            const sidebar = document.querySelector('nav.pc-sidebar');
            const container = document.querySelector('.pc-container');

            // Theme Toggle
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const expanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !expanded);
                    themeDropdown.classList.toggle('hidden');
                });
            }

            // User Menu
            if (userMenu) {
                userMenu.addEventListener('click', function() {
                    const expanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !expanded);
                    userDropdown.classList.toggle('hidden');

                    if (themeDropdown && !themeDropdown.classList.contains('hidden')) {
                        themeDropdown.classList.add('hidden');
                        themeToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (themeToggle && !themeToggle.contains(event.target) &&
                    themeDropdown && !themeDropdown.contains(event.target)) {
                    themeDropdown.classList.add('hidden');
                    themeToggle.setAttribute('aria-expanded', 'false');
                }

                if (userMenu && !userMenu.contains(event.target) &&
                    userDropdown && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                    userMenu.setAttribute('aria-expanded', 'false');
                }
            });

            // Mobile Menu Toggle
            if (mobileToggleBtn) {
                mobileToggleBtn.addEventListener('click', function(event) {
                    sidebar.classList.add('mob-sidebar-active');
                });
            }

            if (container) {
                container.addEventListener('click', function(event) {
                    sidebar.classList.remove('mob-sidebar-active');
                });
            }
        }, {
            once: true
        });
    </script>

    <!-- PWA Service Worker Registration -->
    @if (config('laravelpwa.enabled'))
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/serviceworker.js')
                        .then(function(registration) {
                            console.log('ServiceWorker registration successful with scope: ', registration.scope);
                        }, function(err) {
                            console.log('ServiceWorker registration failed: ', err);
                        });
                });
            }
        </script>
    @endif

    <!-- Stacked Scripts and Livewire -->
    @stack('scripts')
    @livewireScripts
</body>

</html>
