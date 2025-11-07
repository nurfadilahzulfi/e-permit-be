<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-R">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Permit Dashboard')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="//unpkg.com/alpinejs" defer></script>
    
    @stack('styles')
</head>
<body class="font-inter antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300">

    @php
    $activeClass = 'bg-blue-600 text-white shadow-lg';
    $inactiveClass = 'hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300';
    @endphp

    <aside id="sidebar" 
           class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-slate-900 shadow-xl transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40
                  flex flex-col">
        
        <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-2">
                <span class="inline-block p-2 bg-blue-600 text-white rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M7 12h10"/>
                    </svg>
                </span>
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">E-Permit</span>
            </a>
            <button id="closeSidebar" class="md:hidden text-2xl text-slate-600 dark:text-slate-300">&times;</button>
        </div>

        <nav class="p-4 space-y-2 flex-1 overflow-y-auto">
            
            <a href="{{ url('/dashboard') }}" 
               class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                      {{ Request::is('dashboard') ? $activeClass : $inactiveClass }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                <span class="font-medium">Dashboard</span>
            </a>

            {{-- =================================== --}}
            {{-- GRUP MENU: PROSES IZIN (ALUR BARU) --}}
            {{-- =================================== --}}
            @php
            // [DIUBAH] Tambahkan URL 'work-permit-completion'
            $isProsesIzinActive = Request::is('dashboard/work-permit*') || 
                                  Request::is('dashboard/my-permits*') || 
                                  Request::is('dashboard/work-permit-approval*') || 
                                  Request::is('dashboard/work-permit-completion*') || // <-- [BARU]
                                  Request::is('gwp-cek/view*');
            @endphp
            <div x-data="{ open: {{ $isProsesIzinActive ? 'true' : 'false' }} }">
                
                {{-- Tombol Dropdown Utama --}}
                <button @click="open = !open" 
                        class="w-full flex items-center justify-between gap-3 py-2.5 px-4 rounded-lg transition-all duration-200 {{ $inactiveClass }}">
                    {{-- ... (Icon dan Teks "Proses Izin") ... --}}
                </button>
                
                {{-- Konten Dropdown (Sub-menu) --}}
                <div x-show="open" x-transition class="pl-5 space-y-1 mt-1">
                    
                    {{-- Hanya HSE & Admin --}}
                    @if(in_array(Auth::user()->role, ['hse', 'admin']))
                    <a href="{{ route('dashboard.work-permit') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/work-permit') ? $activeClass : $inactiveClass }}">
                        Inisiasi Izin Kerja
                    </a>
                    @endif
                    
                    {{-- Hanya Pemohon --}}
                    @if(in_array(Auth::user()->role, ['pemohon']))
                    <a href="{{ route('dashboard.my-permits') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/my-permits*') ? $activeClass : $inactiveClass }}">
                        Tugas Izin Saya
                    </a>
                    @endif
                    
                    {{-- Hanya Supervisor, HSE, & Admin --}}
                    @if(in_array(Auth::user()->role, ['supervisor', 'hse', 'admin']))
                    <a href="{{ route('dashboard.work-permit-approval') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/work-permit-approval*') ? $activeClass : $inactiveClass }}">
                        Persetujuan Izin
                    </a>
                    @endif

                    {{-- [BARU] Dilihat oleh semua role --}}
                    @if(in_array(Auth::user()->role, ['supervisor', 'hse', 'pemohon']))
                    <a href="{{ route('dashboard.work-permit-completion') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/work-permit-completion*') ? $activeClass : $inactiveClass }}">
                        Pengesahan Selesai
                    </a>
                    @endif
                    
                </div>
            </div>
            {{-- =================================== --}}
            {{-- GRUP MENU: ADMIN / MASTER DATA --}}
            {{-- =================================== --}}
            @if(Auth::user()->role == 'admin')
            @php
            // Cek URL untuk dropdown
            $isMasterDataActive = Request::is('dashboard/user*') || Request::is('dashboard/permit-types*') || Request::is('dashboard/gwp-cek-pemohon-ls*') || Request::is('dashboard/gwp-cek-hse-ls*') || Request::is('dashboard/gwp-alat-ls*');
            @endphp
            <div x-data="{ open: {{ $isMasterDataActive ? 'true' : 'false' }} }">
                {{-- Tombol Dropdown Utama --}}
                <button @click="open = !open" 
                        class="w-full flex items-center justify-between gap-3 py-2.5 px-4 rounded-lg transition-all duration-200 {{ $inactiveClass }}">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 0 2l-.15.1a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1 0-2l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                        <span class="font-medium">Master Data</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 transition-transform" :class="{'rotate-90': open}"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                
                {{-- Konten Dropdown (Sub-menu) --}}
                <div x-show="open" x-transition class="pl-5 space-y-1 mt-1">
                    <a href="{{ route('dashboard.user') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/user*') ? $activeClass : $inactiveClass }}">
                        Manage User
                    </a>
                    <a href="{{ route('dashboard.permit-types') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/permit-types*') ? $activeClass : $inactiveClass }}">
                        Jenis Izin
                    </a>
                    <a href="{{ route('dashboard.gwp-cek-pemohon-ls') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/gwp-cek-pemohon-ls*') ? $activeClass : $inactiveClass }}">
                        Checklist Pemohon
                    </a>
                    <a href="{{ route('dashboard.gwp-cek-hse-ls') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/gwp-cek-hse-ls*') ? $activeClass : $inactiveClass }}">
                        Checklist HSE
                    </a>
                    <a href="{{ route('dashboard.gwp-alat-ls') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/gwp-alat-ls*') ? $activeClass : $inactiveClass }}">
                        Checklist Alat
                    </a>
                </div>
            </div>
            @endif
            
        </nav>

        <div class="border-t border-slate-200 dark:border-slate-700 p-4">
            
            <a href="{{ route('profile.show') }}" 
               class="w-full flex items-center gap-3 py-2.5 px-4 rounded-lg font-medium 
                      text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 
                      transition-all duration-200 mb-2
                      {{ Request::is('profile*') ? $activeClass : $inactiveClass }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                <span>Edit Profile</span>
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center gap-3 py-2.5 px-4 rounded-lg font-medium text-red-500 hover:bg-red-100 dark:hover:bg-red-900/50 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 hidden md:hidden z-30"></div>

    <div class="md:ml-64 min-h-screen flex flex-col transition-all duration-300">

        <header class="flex items-center justify-between bg-white dark:bg-slate-900 shadow-sm px-4 sm:px-6 py-3 sticky top-0 z-20 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <button id="menuButton" class="md:hidden text-2xl text-slate-500 dark:text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-slate-700 dark:text-slate-200">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-500 dark:text-slate-400 hidden sm:inline">
                    Hai, <strong class="font-medium">{{ Auth::user()->nama ?? 'User' }}</strong>
                </span>

                <button id="themeToggle" 
                        class="w-10 h-10 rounded-full flex items-center justify-center 
                               bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 
                               text-slate-600 dark:text-slate-300 transition-all">
                    <svg id="themeIconMoon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                    <svg id="themeIconSun" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 hidden"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                </button>

                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm" 
                     title="{{ Auth::user()->nama ?? 'User' }}">
                    {{ substr(Auth::user()->nama ?? 'U', 0, 1) }}
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 flex-1">
            @yield('content')
        </main>

        <footer class="text-center py-4 text-sm text-slate-500 dark:text-slate-400 border-t border-slate-200 dark:border-slate-800
                         bg-white dark:bg-slate-900">
            &copy; {{ date('Y') }} <strong>E-Permit System</strong> | All Rights Reserved
        </footer>
    </div>

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