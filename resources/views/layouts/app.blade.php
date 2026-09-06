<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Management System' }} | {{ config('app.name', 'Paolo Paolo D.A Matting & Accessories') }}</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS with Dark & Light Mode -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        dark: {
                            900: '#090d16',
                            850: '#0e1526',
                            800: '#141e34',
                            700: '#1e293b',
                            600: '#334155',
                        }
                    }
                }
            }
        }
    </script>

    
    <script src="https://cdn.jsdelivr.net/npm/@formkit/auto-animate@0.8.2/index.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>

    <style>
        /* Theme Transition */
        html.dark {
            color-scheme: dark;
        }
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.25s ease, color 0.25s ease;
        }
        h1, h2, h3, h4, .font-display {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbars */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #0284c7;
        }
        .glass-card {
            backdrop-filter: blur(12px);
        }
        .dark .glass-card {
            background: rgba(20, 30, 52, 0.85);
            border: 1px solid rgba(56, 189, 248, 0.14);
        }
        .dark .glass-card:hover {
            border-color: rgba(56, 189, 248, 0.35);
        }
        html:not(.dark) .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px -2px rgba(0, 0, 0, 0.05);
        }
        html:not(.dark) .glass-card:hover {
            border-color: #0284c7;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen antialiased flex flex-col bg-slate-100 text-slate-800 dark:bg-[#090d16] dark:text-slate-100 text-base selection:bg-brand-500 selection:text-white">

    <!-- TOP PERMANENT SLIM BAR -->
    <header class="h-20 bg-white/95 dark:bg-[#0c1222]/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0 z-40 px-5 sm:px-8 flex items-center justify-between shadow-sm">
        
        <!-- Left: MENU BUTTON FIRST, THEN LOGO -->
        <div class="flex items-center gap-3 sm:gap-4">
            <!-- Menu Toggle Button on the Left Side -->
            <button type="button" id="menuToggleBtn" title="Open navigation menu"
                class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-dark-800 dark:hover:bg-dark-700 text-slate-800 dark:text-slate-100 border border-slate-300 dark:border-slate-700 font-bold text-sm shadow-sm transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
                <i class="fas fa-bars text-base text-cyan-500"></i>
                <span class="font-display tracking-wide">Menu</span>
            </button>

            <!-- Brand Logo & Name (Clicking navigates to Dashboard) -->
            <a href="{{ route('dashboard') }}" title="Go to Dashboard"
                class="flex items-center gap-3 group p-1.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-dark-800/60 transition-all cursor-pointer focus:outline-none">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-brand-600 via-cyan-500 to-blue-600 p-0.5 shadow-lg shadow-cyan-500/20 group-hover:scale-105 transition-transform flex items-center justify-center">
                    <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center text-cyan-400 font-black text-xl sm:text-2xl font-display">
                        PP
                    </div>
                </div>
                <div class="text-left hidden xs:block">
                    <div class="font-display font-black text-slate-900 dark:text-white text-base sm:text-lg tracking-wider group-hover:text-cyan-500 transition-colors">
                        PAOLO PAOLO
                    </div>
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 -mt-0.5">
                        Matting &amp; Accessories
                    </div>
                </div>
            </a>
        </div>

        <!-- Right: Actions, Theme Switcher, Quick Info, Profile -->
        <div class="flex items-center gap-3.5">
            <!-- Launch POS Button -->
            <a href="{{ route('pos.index') }}" class="flex items-center gap-2.5 px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm shadow-md shadow-cyan-500/20 transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-cash-register text-base"></i>
                <span class="hidden sm:inline">POS Terminal</span>
            </a>

            <!-- Light / Dark Mode Toggle Switch -->
            <button type="button" id="themeToggleBtn" title="Toggle Light / Dark Mode"
                class="w-11 h-11 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-amber-300 hover:bg-slate-300 dark:hover:bg-dark-700 border border-slate-300 dark:border-slate-700 flex items-center justify-center text-lg transition-colors">
                <i id="themeIcon" class="fas fa-sun"></i>
            </button>

            <!-- User Chip & Logout -->
            <div class="flex items-center gap-3 pl-2 border-l border-slate-300 dark:border-slate-800">
                <div class="hidden md:block text-right">
                    <div class="text-sm font-bold text-slate-900 dark:text-white leading-tight">{{ auth()->user()->name }}</div>
                    <span class="text-xs font-semibold uppercase tracking-wider {{ auth()->user()->isAdmin() ? 'text-purple-600 dark:text-purple-400' : 'text-cyan-600 dark:text-cyan-400' }}">
                        {{ auth()->user()->role }}
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Log out of system" class="w-11 h-11 rounded-xl bg-slate-200 dark:bg-dark-800 hover:bg-rose-100 dark:hover:bg-rose-950/40 text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 border border-slate-300 dark:border-slate-700 flex items-center justify-center text-base transition-colors">
                        <i class="fas fa-power-off"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- SLIDING NAVIGATION DRAWER (Activated by Logo Click) -->
    <div id="navDrawerBackdrop" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden transition-opacity duration-300"></div>

    <aside id="navDrawer" class="fixed inset-y-0 left-0 z-50 w-80 sm:w-96 bg-white dark:bg-[#0c1222] border-r border-slate-200 dark:border-slate-800 shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <!-- Drawer Header -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-cyan-400 p-0.5 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <div class="w-full h-full bg-slate-900 rounded-[10px] flex items-center justify-center text-cyan-400 font-bold text-lg font-display">
                        PP
                    </div>
                </div>
                <div>
                    <h3 class="font-display font-black text-slate-900 dark:text-white text-base group-hover:text-cyan-500 transition-colors">Paolo Paolo</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Matting &amp; Accessories</p>
                </div>
            </a>
            <button type="button" id="closeDrawerBtn" class="w-9 h-9 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-dark-800 flex items-center justify-center text-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-5 space-y-2 overflow-y-auto" id="drawerNavLinks">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 px-3 pt-2 pb-1">Main Modules</div>

            <a href="{{ route('dashboard') }}" class="nav-module-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-base font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800' }}">
                <i class="fas fa-chart-pie w-6 text-center text-lg {{ request()->routeIs('dashboard') ? 'text-cyan-500' : 'text-slate-400' }}"></i>
                <span>Executive Dashboard</span>
            </a>

            <a href="{{ route('pos.index') }}" class="nav-module-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-base font-semibold transition-all {{ request()->routeIs('pos.*') ? 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800' }}">
                <i class="fas fa-cash-register w-6 text-center text-lg {{ request()->routeIs('pos.*') ? 'text-cyan-500' : 'text-slate-400' }}"></i>
                <span>POS / Cashier Terminal</span>
            </a>

            <a href="{{ route('products.index') }}" class="nav-module-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-base font-semibold transition-all {{ (request()->routeIs('products.*') || request()->routeIs('inventory.*')) ? 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800' }}">
                <i class="fas fa-boxes-stacked w-6 text-center text-lg {{ (request()->routeIs('products.*') || request()->routeIs('inventory.*')) ? 'text-cyan-500' : 'text-slate-400' }}"></i>
                <span>Inventory</span>
            </a>

            <!-- Customer Records (Accessible by Cashier and Admin) -->
            <a href="{{ route('customers.index') }}" class="nav-module-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-base font-semibold transition-all {{ request()->routeIs('customers.*') ? 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800' }}">
                <i class="fas fa-id-card w-6 text-center text-lg {{ request()->routeIs('customers.*') ? 'text-cyan-500' : 'text-slate-400' }}"></i>
                <span>Customer Records</span>
            </a>

            @if(auth()->user()->isAdmin())
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 px-3 pt-4 pb-1">Admin Controls</div>

            <a href="{{ route('stock-in.index') }}" class="nav-module-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-base font-semibold transition-all {{ request()->routeIs('stock-in.*') ? 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800' }}">
                <i class="fas fa-truck-loading w-6 text-center text-lg {{ request()->routeIs('stock-in.*') ? 'text-cyan-500' : 'text-slate-400' }}"></i>
                <span>Stock-In Receiving</span>
            </a>

            <a href="{{ route('backup.index') }}" class="nav-module-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-base font-semibold transition-all {{ request()->routeIs('backup.*') ? 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800' }}">
                <i class="fas fa-hard-drive w-6 text-center text-lg {{ request()->routeIs('backup.*') ? 'text-cyan-500' : 'text-slate-400' }}"></i>
                <span>Backup Module (E: Drive)</span>
            </a>

            <a href="{{ route('users.index') }}" class="nav-module-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-base font-semibold transition-all {{ request()->routeIs('users.*') ? 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-dark-800' }}">
                <i class="fas fa-user-shield w-6 text-center text-lg {{ request()->routeIs('users.*') ? 'text-cyan-500' : 'text-slate-400' }}"></i>
                <span>Staff &amp; Users</span>
            </a>
            @endif
        </nav>
    </aside>

    <!-- Flash Messages & Alerts -->
    <div class="max-w-7xl mx-auto w-full px-5 sm:px-8 pt-4" data-auto-animate>
        @if(session('success'))
        <div class="p-4 mb-4 rounded-2xl bg-emerald-100 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-md">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 text-xl"></i>
                <span class="text-base font-medium">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400/60 hover:text-emerald-800 text-lg">&times;</button>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 mb-4 rounded-2xl bg-rose-100 dark:bg-rose-950/70 border border-rose-300 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-md">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-rose-600 dark:text-rose-400 text-xl"></i>
                <span class="text-base font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400/60 hover:text-rose-800 text-lg">&times;</button>
        </div>
        @endif

        @if($errors->any())
        <div class="p-4 mb-4 rounded-2xl bg-amber-100 dark:bg-amber-950/70 border border-amber-300 dark:border-amber-500/30 text-amber-900 dark:text-amber-300 shadow-md">
            <div class="flex items-center gap-3 mb-1.5 font-bold text-base">
                <i class="fas fa-triangle-exclamation text-amber-600 dark:text-amber-400 text-xl"></i>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc list-inside text-sm space-y-1 pl-6">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- MAIN PAGE CONTENT (100% Full Width of Screen) -->
    <main class="flex-1 max-w-[1700px] w-full mx-auto px-5 sm:px-8 py-5">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-4 px-6 border-t border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 text-center flex flex-col sm:flex-row items-center justify-between gap-2">
        <div>
            &copy; {{ date('Y') }} <strong class="text-slate-700 dark:text-slate-200">Paolo Paolo D.A Matting &amp; Accessories</strong>. All rights reserved.
        </div>
    </footer>

    <!-- Global JavaScript: Sliding Drawer & Light/Dark Theme Switcher -->
    <script>
        // 1. Light / Dark Theme Management with Persistence
        const themeBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');

        function setTheme(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('paolo_theme', 'dark');
                themeIcon.className = 'fas fa-sun text-amber-400';
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('paolo_theme', 'light');
                themeIcon.className = 'fas fa-moon text-slate-700';
            }
        }

        // Initialize Theme from storage or preference
        const savedTheme = localStorage.getItem('paolo_theme');
        if (savedTheme === 'light') {
            setTheme(false);
        } else {
            setTheme(true);
        }

        if (themeBtn) {
            themeBtn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.contains('dark');
                setTheme(!isDark);
            });
        }

        // 2. Sliding Navigation Drawer (Menu Button Click Opens, Module Click Closes)
        const menuBtn = document.getElementById('menuToggleBtn');
        const drawer = document.getElementById('navDrawer');
        const backdrop = document.getElementById('navDrawerBackdrop');
        const closeBtn = document.getElementById('closeDrawerBtn');

        function openDrawer() {
            drawer.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        }

        function closeDrawer() {
            drawer.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }

        if (menuBtn) menuBtn.addEventListener('click', openDrawer);
        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
        if (backdrop) backdrop.addEventListener('click', closeDrawer);

        // Auto-close when clicking any link inside drawer so screen is wide open
        document.querySelectorAll('.nav-module-link').forEach(link => {
            link.addEventListener('click', () => {
                closeDrawer();
            });
        });

        // Initialize AutoAnimate
        document.addEventListener('DOMContentLoaded', () => {
            if (window.autoAnimate) {
                document.querySelectorAll('[data-auto-animate]').forEach(el => {
                    window.autoAnimate(el);
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
