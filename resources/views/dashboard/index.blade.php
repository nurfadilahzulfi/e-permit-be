@extends('layouts.app')

@section('title', 'Dashboard E-Permit')
@section('page-title', 'Dashboard')

@section('content')
{{-- DIUBAH: Menambahkan warna teks eksplisit untuk light & dark mode --}}
<h2 class="text-2xl font-bold mb-6 text-slate-900 dark:text-slate-100">
    Selamat Datang, {{ Auth::user()->nama ?? 'User' }} 👋
</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @php
        // DIUBAH: Menambahkan key 'roles' pada setiap menu
        // Role: 'admin', 'supervisor', 'hse', 'pemohon'
        $menus = [
            ['url' => 'user', 'icon' => '👥', 'color' => 'indigo', 'title' => 'Manage User', 'desc' => 'Kelola data pengguna aplikasi', 'roles' => ['admin']],
            ['url' => 'permit-types', 'icon' => '📄', 'color' => 'green', 'title' => 'Permit Types', 'desc' => 'Jenis-jenis izin kerja', 'roles' => ['admin']],
            ['url' => 'permit-gwp', 'icon' => '🧾', 'color' => 'blue', 'title' => 'Permit GWP', 'desc' => 'Data izin kerja GWP', 'roles' => ['pemohon', 'admin']],
            ['url' => 'permit-gwp-approval', 'icon' => '✅', 'color' => 'yellow', 'title' => 'GWP Approvals', 'desc' => 'Persetujuan izin kerja', 'roles' => ['supervisor', 'hse', 'admin']],
            ['url' => 'permit-gwp-completion', 'icon' => '📋', 'color' => 'red', 'title' => 'GWP Completion', 'desc' => 'Penyelesaian izin kerja', 'roles' => ['pemohon', 'admin']], // Asumsi pemohon & admin
            ['url' => 'gwp-cek', 'icon' => '🧠', 'color' => 'gray', 'title' => 'GWP Checklist', 'desc' => 'Pemeriksaan izin GWP', 'roles' => ['admin']], // Asumsi admin
            
        ];
    @endphp

    @foreach($menus as $menu)
        
        {{-- DIUBAH: Tambahkan @if untuk mengecek role user --}}
        @if(in_array(Auth::user()->role, $menu['roles']))
            
            <a href="{{ url($menu['url']) }}" 
               class="bg-white dark:bg-slate-800 rounded-xl shadow 
                      dark:border dark:border-slate-700 
                      hover:shadow-lg hover:-translate-y-1 
                      dark:hover:bg-slate-700
                      transform transition-all duration-200 p-6 text-center">
                
                <div class="text-4xl mb-2">{{ $menu['icon'] }}</div>
                
                <h2 class="text-lg font-semibold text-{{ $menu['color'] }}-600 dark:text-{{ $menu['color'] }}-400">
                    {{ $menu['title'] }}
                </h2>
                
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ $menu['desc'] }}
                </p>
            </a>
            
        @endif {{-- Akhir dari cek role --}}

    @endforeach
</div>
@endsection
