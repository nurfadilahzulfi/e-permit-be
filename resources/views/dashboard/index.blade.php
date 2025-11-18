@extends('layouts.app')

@section('title', 'Dashboard E-Permit')
@section('page-title', 'Dashboard')

@section('content')
<h2 class="text-2xl font-bold mb-6 text-slate-900 dark:text-slate-100">
    Selamat Datang, {{ Auth::user()->nama ?? 'User' }} 👋
</h2>

{{-- 1. KONTEN BARU: STATISTIK (Dibuat Interaktif) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    
    {{-- Card 1: Total User (Interaktif) --}}
    {{-- Ganti '#' dengan route yang sesuai, misal: route('users.index') --}}
    <a href="#" class="block bg-white dark:bg-slate-800 rounded-xl shadow p-6 dark:border dark:border-slate-700 transition-all duration-300 ease-in-out hover:shadow-lg hover:-translate-y-1">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-indigo-600 dark:text-indigo-300">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 dark:text-slate-400">Total User</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                    {{ $stats['total_users'] ?? 0 }}
                </p>
            </div>
        </div>
    </a>

    {{-- Card 2: Izin Pending (Interaktif) --}}
    {{-- Ganti '#' dengan route yang sesuai, misal: route('permits.index', ['status' => 'pending']) --}}
    <a href="#" class="block bg-white dark:bg-slate-800 rounded-xl shadow p-6 dark:border dark:border-slate-700 transition-all duration-300 ease-in-out hover:shadow-lg hover:-translate-y-1">
        <div class="flex items-center gap-4">
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
    </a>

    {{-- Card 3: Izin Disetujui (Interaktif) --}}
    {{-- Ganti '#' dengan route yang sesuai, misal: route('permits.index', ['status' => 'approved']) --}}
    <a href="#" class="block bg-white dark:bg-slate-800 rounded-xl shadow p-6 dark:border dark:border-slate-700 transition-all duration-300 ease-in-out hover:shadow-lg hover:-translate-y-1">
        <div class="flex items-center gap-4">
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
    </a>
    
    {{-- Card 4: Izin Ditolak (Interaktif) --}}
    {{-- Ganti '#' dengan route yang sesuai, misal: route('permits.index', ['status' => 'rejected']) --}}
    <a href="#" class="block bg-white dark:bg-slate-800 rounded-xl shadow p-6 dark:border dark:border-slate-700 transition-all duration-300 ease-in-out hover:shadow-lg hover:-translate-y-1">
        <div class="flex items-center gap-4">
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
    </a>
</div>

{{-- 2. KONTEN BARU: CHART & AKTIVITAS TERBARU --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mt-6">

    <!-- Kolom Kiri: Chart -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl shadow p-6 dark:border dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Ringkasan Status Izin</h3>
        <!-- Container untuk Chart -->
        <div id="permitStatusChart" class="min-h-[300px] w-full"></div>
    </div>

    <!-- Kolom Kanan: Aktivitas Terbaru -->
    <div class="lg:col-span-3 bg-white dark:bg-slate-800 rounded-xl shadow p-6 dark:border dark:border-slate-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Permohonan Terbaru</h3>
            {{-- Link untuk melihat semua permohonan --}}
            <a href="{{-- route('permits.index') --}}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                Lihat Semua
            </a>
        </div>
        
        <!-- Tabel Aktivitas -->
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-slate-500 dark:text-slate-400 uppercase bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-4 py-3">Pemohon</th>
                        <th class="px-4 py-3">Jenis Izin</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 
                        INI ADALAH LOOP DATA YANG SEBENARNYA.
                        Loop ini sekarang aktif dan akan memproses variabel $recent_permits
                        yang dikirim dari AuthController.
                    --}}
                    
                    @forelse($recent_permits as $permit)
                    <tr class="border-b dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                       <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            {{-- [INI JAWABANNYA] Menampilkan nama pemohon --}}
                            {{ $permit->pemohon ? $permit->pemohon->nama : 'N/A' }} 
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-800 dark:text-slate-100 max-w-xs truncate" title="{{ $permit->deskripsi_pekerjaan }}">
                                {{ $permit->deskripsi_pekerjaan }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $permit->lokasi }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{-- Ganti 'jenis_izin' dengan nama kolom yang benar di tabel WorkPermit --}}
                            {{ $permit->jenis_izin ?? 'Izin Kerja' }} 
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            {{-- Menampilkan kapan data dibuat dengan format 'Hari Bulan Tahun' --}}
                            {{ $permit->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            {{-- Ini adalah logika untuk menampilkan status berdasarkan angka --}}
                            {{-- Angka ini harus SAMA PERSIS dengan yang di AuthController --}}
                            @if(in_array($permit->status, [1, 2]))
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                    Pending
                                </span>
                            @elseif($permit->status == 3)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    Disetujui
                                </span>
                            @elseif($permit->status == 4)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                    Ditolak
                                </span>
                            @else
                                 {{-- Fallback jika statusnya aneh/tidak dikenal --}}
                                 <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-300">
                                    Lainnya
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    {{-- 
                        Bagian ini sekarang akan otomatis tampil JIKA
                        $recent_permits dari controller benar-benar kosong.
                    --}}
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">
                            Belum ada permohonan.
                        </td>
                    </tr>
                    @endforelse
                    
                    {{-- DATA CONTOH (John Doe, dll) SUDAH DIHAPUS DARI SINI --}}
                    
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Load library ApexCharts --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // Pastikan skrip berjalan setelah DOM selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Ambil data dari variabel Blade
        // Kita gunakan '?? 0' untuk nilai default jika variabel $stats tidak terdefinisi
        const pending = {{ $stats['pending_permits'] ?? 0 }};
        const approved = {{ $stats['approved_permits'] ?? 0 }};
        const rejected = {{ $stats['rejected_permits'] ?? 0 }};
        const total = pending + approved + rejected;

        // 2. Deteksi Dark Mode dari Tailwind
        // Ini adalah cara standar Tailwind menyimpan preferensi mode
        const isDarkMode = document.documentElement.classList.contains('dark') || 
                           (localStorage.theme === 'dark') || 
                           (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

        // 3. Opsi untuk Chart
        const chartOptions = {
            // Data series: [Pending, Disetujui, Ditolak]
            series: total > 0 ? [pending, approved, rejected] : [1], // Tampilkan chart abu-abu jika total 0
            
            chart: {
                type: 'donut', // Tipe chart: donut
                height: 300,   // Tinggi chart
                toolbar: {
                    show: false // Sembunyikan toolbar
                }
            },
            
            // Label untuk setiap bagian
            labels: total > 0 ? ['Izin Pending', 'Izin Disetujui', 'Izin Ditolak'] : ['Belum Ada Data'],
            
            // Warna. Kita sesuaikan dengan warna kartu statistik
            colors: total > 0 ? ['#f59e0b', '#22c55e', '#ef4444'] : ['#6b7280'], // Kuning (warning-500), Hijau (green-500), Merah (red-500), Abu-abu (gray-500)

            // Pengaturan legenda
            legend: {
                position: 'bottom', // Posisi legenda di bawah
                // Atur warna teks legenda berdasarkan mode gelap/terang
                labels: {
                    colors: isDarkMode ? '#cbd5e1' : '#475569' // Warna teks (slate-300 : slate-600)
                }
            },

            // Responsif
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: '100%'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            
            // Atur tema (gelap/terang) untuk tooltip, dll.
            theme: {
                mode: isDarkMode ? 'dark' : 'light'
            },

            // Menghilangkan stroke/border antar slice
            stroke: {
                show: false
            },

            // Tampilkan data label (angka) di dalam chart
            dataLabels: {
                enabled: true,
                formatter: function (val, opts) {
                    // Tampilkan angka absolut-nya
                    if (total > 0) {
                        return opts.w.config.series[opts.seriesIndex]
                    } else {
                        return '' // Sembunyikan label jika tidak ada data
                    }
                },
                style: {
                    colors: ['#fff'], // Warna teks data label
                    fontSize: '14px',
                    fontWeight: 'bold',
                },
                dropShadow: {
                    enabled: false
                }
            },
            
            // Konfigurasi plot (untuk donut)
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%', // Ukuran lubang donut
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Izin',
                                color: isDarkMode ? '#cbd5e1' : '#475569',
                                formatter: function (w) {
                                    return total // Tampilkan total dari semua series
                                }
                            }
                        }
                    }
                }
            },
        };

        // 4. Render Chart
        const chartElement = document.getElementById('permitStatusChart');
        if (chartElement) {
            const chart = new ApexCharts(chartElement, chartOptions);
            chart.render();
            
            // CATATAN: Jika kamu memiliki tombol live-toggle dark mode,
            // kamu perlu menambahkan event listener untuk me-render ulang chart
            // dengan tema yang diperbarui.
        }

    });
</script>
@endpush