<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    {{-- [PERBAIKAN] Typo 'UTF-R' diubah menjadi 'UTF-8' --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Permit Dashboard')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    
    @stack('styles')
</head>
{{-- [PERBAIKAN] Menambahkan 'x-data' untuk mengelola state sidebar --}}
<body class="font-inter antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300"
      x-data="{ sidebarOpen: false }"
      @keydown.escape.window="sidebarOpen = false"
      :class="{ 'overflow-hidden md:overflow-auto': sidebarOpen }">

    @php
    // Class untuk link menu aktif dan non-aktif
    $activeClass = 'bg-blue-600 text-white shadow-lg';
    $inactiveClass = 'hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300';
    @endphp

    {{-- [PERBAIKAN] Sidebar dikontrol oleh 'sidebarOpen' --}}
    <aside id="sidebar" 
           class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-slate-900 shadow-xl transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40
                  flex flex-col"
           :class="{ 'translate-x-0': sidebarOpen }">
        
        <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-2">
                <span class="inline-block p-2 bg-blue-600 text-white rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M7 12h10"/>
                    </svg>
                </span>
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">E-Permit</span>
            </a>
            {{-- [PERBAIKAN] Tombol close kini menggunakan '@click' --}}
            <button id="closeSidebar" class="md:hidden text-2xl text-slate-600 dark:text-slate-300" @click="sidebarOpen = false">&times;</button>
        </div>

        {{-- NAVIGASI (Isi sudah benar dari langkah kita sebelumnya) --}}
        <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
            
            <a href="{{ url('/dashboard') }}" 
               class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                      {{ Request::is('dashboard') ? $activeClass : $inactiveClass }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <p class="px-4 pt-4 pb-2 text-xs font-semibold uppercase text-slate-400 dark:text-slate-500 tracking-wider">
                Proses Izin
            </p>
            
            {{-- Hanya HSE & Admin --}}
            @if(in_array(Auth::user()->role, ['hse', 'admin']))
                <a href="{{ route('dashboard.work-permit-review') }}"
                   class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                          {{ Request::is('dashboard/work-permit-review*') ? $activeClass : $inactiveClass }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                    <span class="font-medium">Tinjauan Izin</span>
                </a>
            @endif
            
            {{-- Hanya Pemohon --}}
            @if(in_array(Auth::user()->role, ['pemohon']))
                @php
                    $isMyPermitsActive = Request::is('dashboard/my-permits*') || 
                                         Request::is('gwp-cek/view*') || 
                                         Request::is('cse-cek/view*') ||
                                         Request::is('hwp-cek/view*') ||
                                         Request::is('ewp-cek/view*') ||
                                         Request::is('lp-cek/view*');
                @endphp
                <a href="{{ route('dashboard.my-permits') }}"
                   class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                          {{ $isMyPermitsActive ? $activeClass : $inactiveClass }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span class="font-medium">Tugas Izin Saya</span>
                </a>
            @endif
            
            {{-- Hanya Supervisor, HSE, & Admin --}}
            @if(in_array(Auth::user()->role, ['supervisor', 'hse', 'admin']))
                <a href="{{ route('dashboard.work-permit-approval') }}"
                   class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                          {{ Request::is('dashboard/work-permit-approval*') ? $activeClass : $inactiveClass }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="m9 14 2 2 4-4"></path></svg>
                    <span class="font-medium">Persetujuan Izin</span>
                </a>
            @endif

            {{-- Dilihat oleh semua role terkait --}}
            @if(in_array(Auth::user()->role, ['supervisor', 'hse', 'pemohon']))
                <a href="{{ route('dashboard.work-permit-completion') }}"
                   class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                          {{ Request::is('dashboard/work-permit-completion*') ? $activeClass : $inactiveClass }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span class="font-medium">Pengesahan Selesai</span>
                </a>
            @endif

            
            {{-- Grup Menu Admin (Sudah lengkap dari langkah sebelumnya) --}}
            @if(Auth::user()->role == 'admin')
            <p class="px-4 pt-4 pb-2 text-xs font-semibold uppercase text-slate-400 dark:text-slate-500 tracking-wider">
                Master Data
            </p>
            
            <a href="{{ route('dashboard.user') }}"
               class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                      {{ Request::is('dashboard/user*') ? $activeClass : $inactiveClass }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span class="font-medium">Manage User</span>
            </a>
            <a href="{{ route('dashboard.permit-types') }}"
               class="flex items-center gap-3 py-2.5 px-4 rounded-lg transition-all duration-200
                      {{ Request::is('dashboard/permit-types*') ? $activeClass : $inactiveClass }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                <span class="font-medium">Jenis Izin</span>
            </a>
            
            @php
                $isChecklistActive = Request::is('dashboard/gwp-cek-pemohon-ls*') || 
                                     Request::is('dashboard/gwp-cek-hse-ls*') || 
                                     Request::is('dashboard/gwp-alat-ls*') ||
                                     Request::is('dashboard/hwp-cek-ls*') ||
                                     Request::is('dashboard/ewp-cek-ls*') ||
                                     Request::is('dashboard/lp-cek-ls*');
            @endphp
            <div x-data="{ open: {{ $isChecklistActive ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="w-full flex items-center justify-between gap-3 py-2.5 px-4 rounded-lg transition-all duration-200 {{ $inactiveClass }}">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        <span class="font-medium">Master Checklist</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 transition-transform" :class="{'rotate-90': open}"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <div x-show="open" x-transition class="pl-5 space-y-1 mt-1">
                    <a href="{{ route('dashboard.gwp-cek-pemohon-ls') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/gwp-cek-pemohon-ls*') ? $activeClass : $inactiveClass }}">
                        Checklist GWP (Pemohon)
                    </a>
                    <a href="{{ route('dashboard.gwp-cek-hse-ls') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/gwp-cek-hse-ls*') ? $activeClass : $inactiveClass }}">
                        Checklist GWP (HSE)
                    </a>
                    <a href="{{ route('dashboard.gwp-alat-ls') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/gwp-alat-ls*') ? $activeClass : $inactiveClass }}">
                        Checklist GWP (Alat)
                    </a>
                    <a href="{{ route('dashboard.hwp-cek-ls') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/hwp-cek-ls*') ? $activeClass : $inactiveClass }}">
                        Checklist HWP
                    </a>
                    <a href="{{ route('dashboard.ewp-cek-ls') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/ewp-cek-ls*') ? $activeClass : $inactiveClass }}">
                        Checklist EWP
                    </a>
                    <a href="{{ route('dashboard.lp-cek-ls') }}"
                       class="flex items-center gap-3 py-2 px-4 rounded-lg transition-all duration-200 text-sm
                              {{ Request::is('dashboard/lp-cek-ls*') ? $activeClass : $inactiveClass }}">
                        Checklist LP
                    </a>
                </div>
            </div>
            @endif
            
        </nav>

        {{-- Footer Sidebar (Profile & Logout) --}}
        <div class="border-t border-slate-200 dark:border-slate-700 p-4">
            
            <a href="{{ route('profile.show') }}" 
               class="w-full flex items-center gap-3 py-2.5 px-4 rounded-lg font-medium 
                      {{ Request::is('profile*') ? $activeClass : $inactiveClass }}
                      transition-all duration-200 mb-2">
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

    {{-- [PERBAIKAN] Overlay kini dikontrol Alpine.js dengan transisi --}}
    <div id="overlay" 
         class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden"
         x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"> {{-- Ditambahkan style="display: none;" untuk FOUC --}}
    </div>

    <div class="md:ml-64 min-h-screen flex flex-col transition-all duration-300">

        <header class="flex items-center justify-between bg-white dark:bg-slate-900 shadow-sm px-4 sm:px-6 py-3 sticky top-0 z-20 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3">
                {{-- [PERBAIKAN] Tombol Menu (Hamburger) kini menggunakan '@click' --}}
                <button id="menuButton" class="md:hidden text-2xl text-slate-500 dark:text-slate-300" @click.stop="sidebarOpen = true">
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

    {{-- [PERBAIKAN] JavaScript manual untuk sidebar dihapus, diganti Alpine.js --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggle = document.getElementById('themeToggle');
            const themeIconMoon = document.getElementById('themeIconMoon');
            const themeIconSun = document.getElementById('themeIconSun');
            const html = document.documentElement;
            
            // === Dark Mode Toggle (Logika ini tetap) ===
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