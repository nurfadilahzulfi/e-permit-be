<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Permit Dashboard')</title>
    
    <!-- Memuat Font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Memuat CSS (Pastikan path ini benar) -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="font-inter antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300">

    @php
    $activeClass = 'bg-blue-600 text-white shadow-lg';
    $inactiveClass = 'hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300';
    @endphp

    <!-- Sidebar -->
    <aside id="sidebar" 
           class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-slate-900 shadow-xl transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40
                  flex flex-col">
        
        <!-- Logo/Header Sidebar -->
        <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-2">
                <!-- Ikon Logo (SVG Inline) -->
                <span class="inline-block p-2 bg-blue-600 text-white rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M7 12h10"/>
                    </svg>
                </span>
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">E-Permit</span>
            </a>
            <button id="closeSidebar" class="md:hidden text-2xl text-slate-600 dark:text-slate-300">&times;</button>
        </div>

        <!-- Navigasi Utama -->
        <nav class="p-4 space-y-2 flex-1 overflow-y-auto">
            <a href="{{ url('/dashboard') }}" 
               class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                      {{ Request::is('dashboard*') ? $activeClass : $inactiveClass }}">
                <!-- Ikon Dashboard (SVG Inline) -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="{{ url('/user') }}" 
               class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                      {{ Request::is('user*') ? $activeClass : $inactiveClass }}">
                <!-- Ikon User (SVG Inline) -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span class="font-medium">Manage User</span>
            </a>
            <a href="{{ url('/permit-types') }}" 
               class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                      {{ Request::is('permit-types*') ? $activeClass : $inactiveClass }}">
                <!-- Ikon Permit Types (SVG Inline) -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/>
                </svg>
                <span class="font-medium">Permit Types</span>
            </a>
            <a href="{{ url('/permit-gwp') }}" 
               class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                      {{ Request::is('permit-gwp*') ? $activeClass : $inactiveClass }}">
                <!-- Ikon Permit GWP (SVG Inline) -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2Z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/>
                </svg>
                <span class="font-medium">Permit GWP</span>
            </a>
        </nav>

        <!-- Bagian Bawah Sidebar (Logout) -->
        <div class="border-t border-slate-200 dark:border-slate-700 p-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center gap-3 py-2.5 px-4 rounded-lg font-medium text-red-500 hover:bg-red-100 dark:hover:bg-red-900/50 transition-all duration-200">
                    <!-- Ikon Logout (SVG Inline) -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay untuk mobile -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 hidden md:hidden z-30"></div>

    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen flex flex-col transition-all duration-300">

        <!-- Navbar (Header Utama) -->
        <header class="flex items-center justify-between bg-white dark:bg-slate-900 shadow-sm px-4 sm:px-6 py-3 sticky top-0 z-20 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <button id="menuButton" class="md:hidden text-2xl text-slate-500 dark:text-slate-300">
                    <!-- Ikon Menu (SVG Inline) -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                        <line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                </button>
                <h1 class="text-lg font-semibold text-slate-700 dark:text-slate-200">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-500 dark:text-slate-400 hidden sm:inline">
                    Hai, <strong class="font-medium">{{ Auth::user()->nama ?? 'User' }}</strong>
                </span>

                <!-- Tombol Dark Mode -->
                <button id="themeToggle" 
                        class="w-10 h-10 rounded-full flex items-center justify-center 
                               bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 
                               text-slate-600 dark:text-slate-300 transition-all">
                    <!-- Ikon Moon (SVG Inline) -->
                    <svg id="themeIconMoon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                    </svg>
                    <!-- Ikon Sun (SVG Inline) -->
                    <svg id="themeIconSun" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 hidden">
                        <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                    </svg>
                </button>

                <!-- Avatar User -->
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm" 
                     title="{{ Auth::user()->nama ?? 'User' }}">
                    {{ substr(Auth::user()->nama ?? 'U', 0, 1) }}
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8 flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="text-center py-4 text-sm text-slate-500 dark:text-slate-400 border-t border-slate-200 dark:border-slate-800
                         bg-white dark:bg-slate-900">
            &copy; {{ date('Y') }} <strong>E-Permit System</strong> | All Rights Reserved
        </footer>
    </div>

    <!-- JS: Sidebar & Dark Mode -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const menuButton = document.getElementById('menuButton');
            const closeSidebarBtn = document.getElementById('closeSidebar');
            const overlay = document.getElementById('overlay');
            const themeToggle = document.getElementById('themeToggle');
            const themeIconMoon = document.getElementById('themeIconMoon');
            const themeIconSun = document.getElementById('themeIconSun');
            const html = document.documentElement;
            
            // === Sidebar Toggle ===
            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            };
            const closeSidebarAction = () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            };

            menuButton.addEventListener('click', openSidebar);
            closeSidebarBtn?.addEventListener('click', closeSidebarAction);
            overlay.addEventListener('click', closeSidebarAction);

            // === Dark Mode Toggle ===
            const updateThemeIcon = (isDark) => {
                if (isDark) {
                    themeIconMoon.classList.add('hidden');
                    themeIconSun.classList.remove('hidden');
                } else {
                    themeIconMoon.classList.remove('hidden');
                    themeIconSun.classList.add('hidden');
                }
            };

            const currentTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (currentTheme === 'dark' || (!currentTheme && prefersDark)) {
                html.classList.add('dark');
                updateThemeIcon(true);
            } else {
                html.classList.remove('dark');
                updateThemeIcon(false);
            }

            themeToggle.addEventListener('click', () => {
                const isDark = html.classList.toggle('dark');
                updateThemeIcon(isDark);
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>

