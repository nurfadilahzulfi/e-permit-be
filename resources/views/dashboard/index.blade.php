@extends('layouts.app')

@section('title', 'Dashboard E-Permit')
@section('page-title', 'Dashboard')

@section('content')
<h2 class="text-2xl font-bold mb-6 text-slate-900 dark:text-slate-100">
    Selamat Datang, {{ Auth::user()->nama ?? 'User' }} 👋
</h2>

{{-- 1. KONTEN BARU: STATISTIK (Menggantikan menu card) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    
    {{-- Card 1: Total User --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 flex items-center gap-4 dark:border dark:border-slate-700">
        <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-indigo-600 dark:text-indigo-300">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Total User</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                {{-- 'stats' dikirim dari AuthController@dashboard --}}
                {{ $stats['total_users'] ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Card 2: Izin Pending --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 flex items-center gap-4 dark:border dark:border-slate-700">
        <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-yellow-600 dark:text-yellow-300">
                <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Izin Pending</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                {{ $stats['pending_permits'] ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Card 3: Izin Disetujui --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 flex items-center gap-4 dark:border dark:border-slate-700">
        <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-green-600 dark:text-green-300">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Izin Disetujui</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                {{ $stats['approved_permits'] ?? 0 }}
            </p>
        </div>
    </div>
    
    {{-- Card 4: Izin Ditolak --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 flex items-center gap-4 dark:border dark:border-slate-700">
        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-red-600 dark:text-red-300">
                <circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Izin Ditolak</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                {{ $stats['rejected_permits'] ?? 0 }}
            </p>
        </div>
    </div>

</div>

{{-- 
    CATATAN: 
    Jika role-nya BUKAN admin (misal: Pemohon atau Supervisor), 
    mereka juga akan melihat statistik ini. 
    
    Kita bisa tambahkan @if(Auth::user()->role == 'admin') ... @else ... @endif 
    di sini jika kamu ingin Pemohon melihat 'kartu menu' yang lama.
    
    Namun, untuk saat ini, statistik ini jauh lebih informatif untuk semua role.
--}}
@endsection