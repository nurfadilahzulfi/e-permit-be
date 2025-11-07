@extends('layouts.app')

@section('title', 'Persetujuan Izin Kerja - E-Permit')
@section('page-title', 'Persetujuan Izin Kerja')

@section('content')

{{-- Wadah untuk notifikasi toast --}}
<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm"></div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">
        Menunggu Persetujuan Anda
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
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Tgl. Dibuat (Log)</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Aksi</th>
                </tr>
            </thead>
            
            <tbody id="tabel-approval" class="divide-y divide-slate-200 dark:divide-slate-700">
                
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-12 h-12 text-slate-300 dark:text-slate-600"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>
                                <span class="text-lg font-medium">Tidak ada izin yang menunggu persetujuan Anda.</span>
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
                            <div class="text-xs text-slate-500 dark:text-slate-400" data-field="urutan_persetujuan"></div>
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
                        {{-- Data Tanggal --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            <span data-field="tanggal_dibuat"></span>
                        </td>
                        {{-- Data Aksi --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2" data-field="aksi-buttons">
                                {{-- Tombol Detail (Checklist GWP) --}}
                                <a href="#" data-action="detail-gwp" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors px-2 py-1.5 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/50" title="Lihat Checklist GWP">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>
                                </a>
                                {{-- Tombol Reject --}}
                                <button data-action="reject" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors px-2 py-1.5 rounded-md hover:bg-red-100 dark:hover:bg-red-900/50" title="Tolak Izin">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                                {{-- Tombol Approve --}}
                                <button data-action="approve" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition-colors px-2 py-1.5 rounded-md hover:bg-green-100 dark:hover:bg-green-900/50" title="Setujui Izin">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </button>
                            </div>
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
    // URL API
    const API_URL = "{{ route('work-permit-approval.index') }}";
    
    // Elemen Global
    const toastContainer = document.getElementById('toast-container');
    const tableBody = document.getElementById('tabel-approval');
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

    function formatTanggal(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('id-ID', { 
                day: 'numeric', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            }).format(date).replace('.', ',');
        } catch (e) { return dateString; }
    }

    // ===========================================
    // FUNGSI UTAMA (CRUD & ACTIONS)
    // ===========================================

    /**
     * Memuat daftar izin yang menunggu persetujuan dari API
     */
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

            // 3. Loop data dan masukkan ke tabel
            result.data.forEach(item => {
                const permit = item.work_permit; 
                if (!permit) return; 

                const row = taskRowTemplate.content.cloneNode(true);
                const newRow = row.querySelector('tr');
                
                newRow.dataset.id = item.id; 
                newRow.dataset.permitId = permit.id; 

                // Isi data ke kolom
                row.querySelector('[data-field="nomor_pekerjaan"]').textContent = permit.nomor_pekerjaan;
                row.querySelector('[data-field="urutan_persetujuan"]').textContent = `Persetujuan Tahap: ${item.urutan}`;
                row.querySelector('[data-field="deskripsi_pekerjaan"]').textContent = permit.deskripsi_pekerjaan;
                row.querySelector('[data-field="deskripsi_pekerjaan"]').title = permit.deskripsi_pekerjaan;
                row.querySelector('[data-field="lokasi_pekerjaan"]').textContent = permit.lokasi;
                row.querySelector('[data-field="nama_pemohon"]').textContent = permit.pemohon ? permit.pemohon.nama : 'N/A';
                row.querySelector('[data-field="tanggal_dibuat"]').textContent = formatTanggal(item.created_at);

                // Event Listeners untuk Tombol Aksi
                const aksiContainer = row.querySelector('[data-field="aksi-buttons"]');
                const gwpButton = aksiContainer.querySelector('[data-action="detail-gwp"]');
                const approveButton = aksiContainer.querySelector('[data-action="approve"]');
                const rejectButton = aksiContainer.querySelector('[data-action="reject"]');

                // Arahkan tombol GWP (jika ada)
                if (permit.permit_gwp) {
                    gwpButton.href = `{{ url('gwp-cek/view') }}/${permit.permit_gwp.id}`;
                } else {
                    gwpButton.remove(); // Hapus tombol GWP jika bukan GWP
                }
                
                {{-- 
                ===================================================
                [INI PERBARUANNYA] Tambahkan tombol untuk CSE
                ===================================================
                --}}
                if (permit.permit_cse) {
                    const cseButton = document.createElement('a');
                    cseButton.href = `{{ url('cse-cek/view') }}/${permit.permit_cse.id}`;
                    cseButton.title = "Lihat Checklist CSE";
                    cseButton.className = "text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 transition-colors px-2 py-1.5 rounded-md hover:bg-purple-100 dark:hover:bg-purple-900/50";
                    cseButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2Z"></path><path d="m12 16-4-4 4-4"></path><path d="M16 12H8"></path></svg>`;
                    // Sisipkan sebelum tombol reject
                    aksiContainer.insertBefore(cseButton, rejectButton);
                }

                approveButton.addEventListener('click', () => handleApprove(item.id));
                rejectButton.addEventListener('click', () => handleReject(item.id));

                tableBody.appendChild(row);
            });

        } catch (e) {
            loadingRow.classList.add('hidden');
            console.error("Error:", e);
            showToast(`Error: ${e.message}`, 'error');
        }
    }

    /**
     * Aksi: Menyetujui Izin
     */
    async function handleApprove(approvalId) {
        if (!confirm('Apakah Anda yakin ingin MENYETUJUI izin ini?')) {
            return;
        }

        try {
            const response = await fetch(`{{ route('work-permit-approval.approve') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    approval_id: approvalId,
                    catatan: 'Disetujui'
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

    /**
     * Aksi: Menolak Izin
     */
    async function handleReject(approvalId) {
        const catatan = prompt('Harap masukkan ALASAN PENOLAKAN:');
        
        if (!catatan) {
            showToast('Penolakan dibatalkan. Alasan wajib diisi.', 'error');
            return;
        }
        if (catatan.length < 5) {
            showToast('Alasan penolakan terlalu pendek.', 'error');
            return;
        }

        try {
            const response = await fetch(`{{ route('work-permit-approval.reject') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    approval_id: approvalId,
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