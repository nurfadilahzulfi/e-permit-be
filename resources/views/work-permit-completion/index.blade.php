@extends('layouts.app')

@section('title', 'Pengesahan Selesai - E-Permit')
@section('page-title', 'Tugas Pengesahan Selesai')

@section('content')

{{-- Wadah untuk notifikasi toast --}}
<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm"></div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">
        Menunggu Tanda Tangan Anda
    </h2>
</div>

<div class="sm:hidden text-sm text-slate-500 dark:text-slate-400 mb-2">
    <span class="font-bold">→</span> Geser tabel ke samping untuk melihat semua kolom.
</div>

{{-- Container Tabel --}}
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            
            <thead class="bg-slate-50 dark:bg-slate-900/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">No. Pekerjaan</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Pekerjaan</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Pemohon</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Tahap Penutupan</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Aksi</th>
                </tr>
            </thead>
            
            <tbody id="tabel-completion" class="divide-y divide-slate-200 dark:divide-slate-700">
                
                {{-- Baris Loading --}}
                <tr id="loading-row">
                    <td colspan="5" class="px-6 py-8 text-center">
                        <div class="flex justify-center items-center gap-3 text-slate-500 dark:text-slate-400">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memuat data...</span>
                        </div>
                    </td>
                </tr>

                {{-- Baris Data Kosong (Template) --}}
                <template id="empty-row-template">
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-12 h-12 text-slate-300 dark:text-slate-600"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                <span class="text-lg font-medium">Tidak ada tugas pengesahan selesai.</span>
                            </div>
                        </td>
                    </tr>
                </template>

                {{-- Baris Data Izin (Template) --}}
                <template id="task-row-template">
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        {{-- Data No. Pekerjaan --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-blue-600 dark:text-blue-400" data-field="nomor_pekerjaan"></div>
                        </td>
                        {{-- Data Pekerjaan --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-800 dark:text-slate-100 max-w-xs truncate" data-field="deskripsi_pekerjaan" title=""></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400" data-field="lokasi_pekerjaan"></div>
                        </td>
                        {{-- Data Pemohon --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            <span data-field="nama_pemohon"></span>
                        </td>
                        {{-- Tahap --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full" data-field="tahap_badge">
                            </span>
                        </td>
                        {{-- Data Aksi --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button data-action="sign" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium shadow-md transition-colors duration-200" disabled>
                                Tanda Tangan
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // [BARU] URL API
    const API_URL = "{{ route('work-permit-completion.index') }}";
    const SIGN_URL = "{{ route('work-permit-completion.sign') }}";
    
    // Elemen Global
    const toastContainer = document.getElementById('toast-container');
    const tableBody = document.getElementById('tabel-completion');
    const loadingRow = document.getElementById('loading-row');
    const taskRowTemplate = document.getElementById('task-row-template');
    const emptyRowTemplate = document.getElementById('empty-row-template');

    // ===========================================
    // UTILITY FUNCTIONS
    // ===========================================
    window.showToast = (message, type = 'success') => {
        const toast = document.createElement('div');
        const bgColor = type === 'success' 
            ? 'bg-gradient-to-r from-green-500 to-emerald-600' 
            : 'bg-gradient-to-r from-red-500 to-rose-600';
        toast.className = `px-5 py-3 rounded-xl shadow-xl text-white text-sm font-medium flex items-center gap-3 ${bgColor} opacity-0 translate-x-10 transition-all duration-300 ease-out`;
        const icon = type === 'success' ? '✅' : '⚠️';
        toast.innerHTML = `<span class="text-lg">${icon}</span><div>${message}</div>`;
        toastContainer.appendChild(toast);
        setTimeout(() => toast.classList.remove('opacity-0', 'translate-x-10'), 10);
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-10');
            toast.addEventListener('transitionend', () => toast.remove());
        }, 5000);
    };

    // ===========================================
    // FUNGSI UTAMA (CRUD & ACTIONS)
    // ===========================================

    async function loadTasks() {
        loadingRow.classList.remove('hidden');
        tableBody.querySelectorAll('tr:not(#loading-row)').forEach(row => row.remove());

        try {
            const response = await fetch(API_URL, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (!response.ok) throw new Error(`Gagal memuat data. Status: ${response.status}`);
            const result = await response.json();
            
            loadingRow.classList.add('hidden');
            if (!result.success || result.data.length === 0) {
                tableBody.appendChild(emptyRowTemplate.content.cloneNode(true));
                return;
            }

            result.data.forEach(item => {
                const row = taskRowTemplate.content.cloneNode(true);
                const newRow = row.querySelector('tr');
                
                newRow.dataset.id = item.completion_id; // ID untuk aksi 'sign'
                
                // Isi data ke kolom
                row.querySelector('[data-field="nomor_pekerjaan"]').textContent = item.nomor_pekerjaan;
                row.querySelector('[data-field="deskripsi_pekerjaan"]').textContent = item.deskripsi_pekerjaan;
                row.querySelector('[data-field="deskripsi_pekerjaan"]').title = item.deskripsi_pekerjaan;
                row.querySelector('[data-field="lokasi_pekerjaan"]').textContent = item.lokasi;
                row.querySelector('[data-field="nama_pemohon"]').textContent = item.pemohon;
                
                // Tahap Badge
                const tahapBadge = row.querySelector('[data-field="tahap_badge"]');
                tahapBadge.textContent = `Tahap: ${item.role_penutupan.toUpperCase()}`;
                tahapBadge.className = `px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full ${item.is_my_turn ? 'bg-blue-200 text-blue-800' : 'bg-slate-200 text-slate-700'}`;

                // Tombol Aksi
                const signButton = row.querySelector('[data-action="sign"]');
                if (item.is_my_turn) {
                    signButton.disabled = false;
                    signButton.addEventListener('click', () => handleSign(item.completion_id));
                } else {
                    signButton.textContent = 'Menunggu';
                    signButton.classList.add('bg-slate-400', 'cursor-not-allowed');
                    signButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                }

                tableBody.appendChild(row);
            });

        } catch (e) {
            loadingRow.classList.add('hidden');
            console.error("Error:", e);
            showToast(`Error: ${e.message}`, 'error');
        }
    }

    /**
     * Aksi: Menandatangani Pengesahan Selesai
     */
    async function handleSign(completionId) {
        const catatan = prompt('Tambahkan catatan (opsional):');
        
        // (User bisa klik Batal, tapi catatan tidak wajib)
        if (catatan === null) { // Jika user klik "Batal"
            return;
        }

        try {
            const response = await fetch(SIGN_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    completion_id: completionId,
                    catatan: catatan
                })
            });

            const result = await response.json();
            if (!result.success) throw new Error(result.error || result.message);

            showToast(result.message, 'success');
            loadTasks(); // Muat ulang daftar
            
        } catch (e) {
            showToast(e.message, 'error');
        }
    }

    // ===========================================
    // INISIALISASI
    // ===========================================
    document.addEventListener('DOMContentLoaded', () => {
        loadTasks();
    });

</script>
@endpush