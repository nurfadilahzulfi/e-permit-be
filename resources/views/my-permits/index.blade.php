@extends('layouts.app')

@section('title', 'Tugas Izin Saya - E-Permit')
@section('page-title', 'Tugas Izin Kerja Saya')

@section('content')

{{-- Wadah untuk notifikasi toast --}}
<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm"></div>

{{-- ... (Header Halaman dan Info Mobile) ... --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">
        Daftar Tugas Izin Kerja
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
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">HSE</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Supervisor</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Aksi</th>
                </tr>
            </thead>
            
            <tbody id="tabel-my-permits" class="divide-y divide-slate-200 dark:divide-slate-700">
                
                {{-- Baris Loading --}}
                <tr id="loading-row">
                    <td colspan="6" class="px-6 py-8 text-center">
                        {{-- ... (Icon Loading) ... --}}
                        <span>Memuat data...</span>
                    </td>
                </tr>

                {{-- Baris Data Kosong (Template) --}}
                <template id="empty-row-template">
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            {{-- ... (Icon Data Kosong) ... --}}
                            <span class="text-lg font-medium">Tidak ada tugas izin kerja untuk Anda.</span>
                        </td>
                    </tr>
                </template>

                {{-- Baris Data Izin (Template) --}}
                <template id="permit-row-template">
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        {{-- No. Pekerjaan --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-blue-600 dark:text-blue-400" data-field="nomor_pekerjaan"></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400" data-field="tgl_pekerjaan_dimulai"></div>
                        </td>
                        {{-- Pekerjaan --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-800 dark:text-slate-100 max-w-xs truncate" data-field="deskripsi_pekerjaan" title=""></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400" data-field="lokasi"></div>
                        </td>
                        {{-- HSE --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            <span data-field="nama_hse"></span>
                        </td>
                        {{-- Supervisor --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            <span data-field="nama_supervisor"></span>
                        </td>
                        {{-- Status --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full" data-field="status_badge">
                            </span>
                        </td>
                        {{-- Aksi --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2" data-field="aksi-buttons">
                                {{-- Tombol Aksi (Detail/Checklist) akan ditambahkan oleh JS --}}
                                {{-- Tombol "Pekerjaan Selesai" akan ditambahkan oleh JS --}}
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
    const API_URL = "{{ route('work-permit.index') }}";
    // [BARU] URL untuk tombol "Pekerjaan Selesai"
    const START_COMPLETION_URL_BASE = "{{ url('work-permit') }}"; // -> /work-permit/{id}/start-completion
    
    // Elemen Global
    const tableBody = document.getElementById('tabel-my-permits');
    const loadingRow = document.getElementById('loading-row');
    const permitRowTemplate = document.getElementById('permit-row-template');
    const emptyRowTemplate = document.getElementById('empty-row-template');
    
    // ===========================================
    // FUNGSI UTAMA (LOAD DATA)
    // ===========================================
    async function loadPermits() {
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

            result.data.forEach(permit => {
                const row = permitRowTemplate.content.cloneNode(true);
                const newRow = row.querySelector('tr');
                newRow.dataset.id = permit.id;

                // Isi data ke kolom
                row.querySelector('[data-field="nomor_pekerjaan"]').textContent = permit.nomor_pekerjaan;
                row.querySelector('[data-field="tgl_pekerjaan_dimulai"]').textContent = `Mulai: ${formatTanggal(permit.tgl_pekerjaan_dimulai)}`;
                row.querySelector('[data-field="deskripsi_pekerjaan"]').textContent = permit.deskripsi_pekerjaan;
                row.querySelector('[data-field="deskripsi_pekerjaan"]').title = permit.deskripsi_pekerjaan;
                row.querySelector('[data-field="lokasi"]').textContent = permit.lokasi;
                row.querySelector('[data-field="nama_hse"]').textContent = permit.hse ? permit.hse.nama : 'N/A';
                row.querySelector('[data-field="nama_supervisor"]').textContent = permit.supervisor ? permit.supervisor.nama : 'N/A';
                
                // Status Badge
                const statusBadge = row.querySelector('[data-field="status_badge"]');
                const { text, color } = getStatusInfo(permit.status);
                statusBadge.textContent = text;
                statusBadge.className = `px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full ${color}`;

                // Tombol Aksi Dinamis
                const aksiContainer = row.querySelector('[data-field="aksi-buttons"]');
                
                // [DIUBAH] Tambahkan tombol berdasarkan status
                
                // 1. Tombol Checklist (GWP, dll)
                // Hanya muncul jika status "Pending Checklist"
                if (permit.status === 1) {
                    if (permit.permit_gwp) {
                        aksiContainer.appendChild(
                            createAksiButton(
                                `{{ url('gwp-cek/view') }}/${permit.permit_gwp.id}`, 
                                'Isi Checklist GWP', 
                                'text-blue-600',
                                `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>`
                            )
                        );
                    }
                    // (Nanti bisa tambahkan 'else if (permit.permit_cse)' dll)
                }
                
                // 2. [BARU] Tombol "Pekerjaan Selesai"
                // Hanya muncul jika status "Approved"
                if (permit.status === 3) {
                    aksiContainer.appendChild(
                        createAksiButton(
                            '#', 
                            'Tandai Pekerjaan Selesai', 
                            'text-green-600',
                            `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`,
                            'start-completion' // <-- [BARU] Data-action
                        )
                    );
                }

                tableBody.appendChild(row);
            });

            // [BARU] Tambahkan event listener untuk tombol baru
            document.querySelectorAll('[data-action="start-completion"]').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const workPermitId = e.currentTarget.closest('tr').dataset.id;
                    handleStartCompletion(workPermitId);
                });
            });

        } catch (e) {
            loadingRow.classList.add('hidden');
            console.error("Error:", e);
        }
    }
    
    /**
     * [BARU] Aksi: Memulai Alur Penutupan (Pekerjaan Selesai)
     */
    async function handleStartCompletion(workPermitId) {
        if (!confirm('Apakah Anda yakin pekerjaan telah selesai dan ingin memulai alur penutupan izin?')) {
            return;
        }

        try {
            const response = await fetch(`${START_COMPLETION_URL_BASE}/${workPermitId}/start-completion`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            if (!result.success) throw new Error(result.error || 'Gagal memulai alur penutupan.');
            
            showToast(result.message, 'success');
            loadPermits(); // Muat ulang tabel (status akan berubah)
            
        } catch (e) {
            showToast(e.message, 'error');
        }
    }

    // ===========================================
    // FUNGSI BANTU (HELPER)
    // ===========================================

    /**
     * [BARU] Helper untuk membuat tombol aksi
     */
    function createAksiButton(href, title, colorClass, svgIcon, action = null) {
        const button = document.createElement(href === '#' ? 'button' : 'a');
        if (href !== '#') button.href = href;
        button.title = title;
        button.className = `${colorClass} hover:opacity-70 transition-colors px-2 py-1.5 rounded-md hover:bg-slate-100 dark:hover:bg-slate-700`;
        button.innerHTML = svgIcon;
        if (action) {
            button.dataset.action = action;
        }
        return button;
    }
    
    window.showToast = (message, type = 'success') => {
        // ... (Fungsi showToast Anda) ...
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
                day: 'numeric', month: 'short', year: 'numeric'
            }).format(date);
        } catch (e) { return dateString; }
    }

    function getStatusInfo(status) {
        // 0=Draft, 1=Pending Checklist, 2=Pending Approval, 3=Approved, 4=Rejected, 
        // 5=Pending HSE Closure, 6=Pending SPV Closure, 7=Pending Pemohon Closure, 8=Closed
        switch (Number(status)) {
            case 0: return { text: 'Draft', color: 'bg-slate-200 text-slate-700' };
            case 1: return { text: 'Pending Checklist', color: 'bg-yellow-200 text-yellow-800' };
            case 2: return { text: 'Pending Approval', color: 'bg-yellow-200 text-yellow-800' };
            case 3: return { text: 'Disetujui', color: 'bg-green-200 text-green-800' };
            case 4: return { text: 'Ditolak', color: 'bg-red-200 text-red-800' };
            case 5: return { text: 'Pending Penutupan HSE', color: 'bg-blue-200 text-blue-800' };
            case 6: return { text: 'Pending Penutupan SPV', color: 'bg-blue-200 text-blue-800' };
            case 7: return { text: 'Pending Penutupan Pemohon', color: 'bg-blue-200 text-blue-800' };
            case 8: return { text: 'Ditutup (Arsip)', color: 'bg-gray-200 text-gray-800' };
            default: return { text: 'Unknown', color: 'bg-slate-200 text-slate-700' };
        }
    }

    // ===========================================
    // INISIALISASI
    // ===========================================
    document.addEventListener('DOMContentLoaded', () => {
        loadPermits();
    });

</script>
@endpush