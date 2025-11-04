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
        $menus = [
            ['url' => 'user', 'icon' => '👥', 'color' => 'indigo', 'title' => 'Manage User', 'desc' => 'Kelola data pengguna aplikasi'],
            ['url' => 'permit-types', 'icon' => '📄', 'color' => 'green', 'title' => 'Permit Types', 'desc' => 'Jenis-jenis izin kerja'],
            ['url' => 'permit-gwp', 'icon' => '🧾', 'color' => 'blue', 'title' => 'Permit GWP', 'desc' => 'Data izin kerja GWP'],
            ['url' => 'permit-gwp-approval', 'icon' => '✅', 'color' => 'yellow', 'title' => 'GWP Approvals', 'desc' => 'Persetujuan izin kerja'],
            ['url' => 'permit-gwp-completion', 'icon' => '📋', 'color' => 'red', 'title' => 'GWP Completion', 'desc' => 'Penyelesaian izin kerja'],
            ['url' => 'gwp-cek', 'icon' => '🧠', 'color' => 'gray', 'title' => 'GWP Checklist', 'desc' => 'Pemeriksaan izin GWP'],
        ];
    @endphp

    @foreach($menus as $menu)
        {{-- 
          DIUBAH: 
          1. Mengganti dark:bg-gray-800 -> dark:bg-slate-800 (Konsistensi)
          2. Menambah dark:border dark:border-slate-700 (Definisi card)
          3. Menambah dark:hover:bg-slate-700 (Efek hover di dark mode)
          4. Menambah transition-all duration-200 (Transisi lebih halus)
        --}}
        <a href="{{ url($menu['url']) }}" 
           class="bg-white dark:bg-slate-800 rounded-xl shadow 
                  dark:border dark:border-slate-700 
                  hover:shadow-lg hover:-translate-y-1 
                  dark:hover:bg-slate-700
                  transform transition-all duration-200 p-6 text-center">
            
            <div class="text-4xl mb-2">{{ $menu['icon'] }}</div>
            
            {{-- Ini sudah benar dari awal --}}
            <h2 class="text-lg font-semibold text-{{ $menu['color'] }}-600 dark:text-{{ $menu['color'] }}-400">
                {{ $menu['title'] }}
            </h2>
            
            {{-- DIUBAH: Mengganti gray -> slate (Konsistensi) --}}
            <p class="text-sm text-slate-500 dark:text-slate-400">
                {{ $menu['desc'] }}
            </p>
        </a>
    @endforeach
</div>
@endsection