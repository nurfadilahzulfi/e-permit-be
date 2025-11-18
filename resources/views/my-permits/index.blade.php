@extends('layouts.app')

@section('title', 'Tugas Izin Saya - E-Permit')
@section('page-title', 'Tugas Izin Kerja Saya')

@section('content')

{{-- Wadah untuk notifikasi toast --}}
<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max_w-sm"></div>

{{-- Header Halaman --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">
        Daftar Izin Kerja Saya
    </h2>
    <button id="addJobBtn"
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg transition duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        <span>Ajukan Pekerjaan Baru</span>
    </button>
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
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-12 h-12 text-slate-300 dark:text-slate-600"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>
                                <span class="text-lg font-medium">Tidak ada tugas izin kerja untuk Anda.</span>
                                <span class="text-sm">Klik "Ajukan Pekerjaan Baru" untuk memulai.</span>
                            </div>
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
                                {{-- Tombol Aksi akan ditambahkan oleh JS --}}
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>


{{-- ========================================== --}}
{{-- MODAL UNTUK AJUKAN PEKERJAAN (Langkah 2) --}}
{{-- ========================================== --}}
<div id="requestJobModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-xl max-h-[90vh] grid grid-rows-[auto_1fr_auto] transform scale-95 transition-transform duration-300">
        
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 dark:text-slate-100">Ajukan Pekerjaan Baru</h3>
            <button id="closeModalBtn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        {{-- Modal Body (Form) --}}
        <form id="requestJobForm" class="overflow-y-auto p-6 space-y-5">
            @csrf
            
            <div>
                <label for="deskripsi_pekerjaan_modal" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                <textarea id="deskripsi_pekerjaan_modal" name="deskripsi_pekerjaan" rows="3" 
                          class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500" 
                          placeholder="Jelaskan pekerjaan yang akan dilakukan..." required></textarea>
                <span id="error_deskripsi_pekerjaan" class="text-xs text-red-500 mt-1 hidden"></span>
            </div>

            <div>
                <label for="lokasi_modal" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Lokasi Pekerjaan <span class="text-red-500">*</span></label>
                <input type="text" id="lokasi_modal" name="lokasi" 
                       class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500" 
                       placeholder="Misal: Area Gedung A, Lantai 2" required>
                <span id="error_lokasi" class="text-xs text-red-500 mt-1 hidden"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="supervisor_id_modal" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Supervisor Pemilik Lokasi <span class="text-red-500">*</span></label>
                    <select id="supervisor_id_modal" name="supervisor_id" class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Memuat...</option>
                    </select>
                    <span id="error_supervisor_id" class="text-xs text-red-500 mt-1 hidden"></span>
                </div>
                <div>
                    <label for="tgl_pekerjaan_dimulai_modal" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Rencana Mulai Tgl <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="tgl_pekerjaan_dimulai_modal" name="tgl_pekerjaan_dimulai" class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600" required>
                    <span id="error_tgl_pekerjaan_dimulai" class="text-xs text-red-500 mt-1 hidden"></span>
                </div>
            </div>
        </form>

        {{-- Modal Footer --}}
        <div class="flex items-center justify-end p-5 border-t border-slate-200 dark:border-slate-700">
            <button id="cancelBtn" 
                    class="px-5 py-2.5 rounded-lg bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-100 text-sm font-medium transition-colors duration-200 mr-3">
                Batal
            </button>
            <button id="saveBtn" 
                    class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md transition-colors duration-200">
                Kirim Pengajuan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // URL API
    const API_URL = "{{ route('work-permit.index') }}";
    const START_COMPLETION_URL_BASE = "{{ url('work-permit') }}";
    const USER_API_URL = "{{ route('user.index') }}";
    const REQUEST_JOB_URL = "{{ route('work-permit.request-job') }}";
    
    // Elemen Global
    const tableBody = document.getElementById('tabel-my-permits');
    const loadingRow = document.getElementById('loading-row');
    const permitRowTemplate = document.getElementById('permit-row-template');
    const emptyRowTemplate = document.getElementById('empty-row-template');
    const toastContainer = document.getElementById('toast-container');
    
    // Elemen Modal
    const addJobBtn = document.getElementById('addJobBtn');
    const modal = document.getElementById('requestJobModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveBtn = document.getElementById('saveBtn');
    const requestJobForm = document.getElementById('requestJobForm');
    const supervisorSelect = document.getElementById('supervisor_id_modal');
    
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

                row.querySelector('[data-field="nomor_pekerjaan"]').textContent = permit.nomor_pekerjaan;
                row.querySelector('[data-field="tgl_pekerjaan_dimulai"]').textContent = `Mulai: ${formatTanggal(permit.tgl_pekerjaan_dimulai)}`;
                row.querySelector('[data-field="deskripsi_pekerjaan"]').textContent = permit.deskripsi_pekerjaan;
                row.querySelector('[data-field="deskripsi_pekerjaan"]').title = permit.deskripsi_pekerjaan;
                row.querySelector('[data-field="lokasi"]').textContent = permit.lokasi;
                row.querySelector('[data-field="nama_hse"]').textContent = permit.hse ? permit.hse.nama : 'N/A';
                row.querySelector('[data-field="nama_supervisor"]').textContent = permit.supervisor ? permit.supervisor.nama : 'N/A';
                
                const statusBadge = row.querySelector('[data-field="status_badge"]');
                const { text, color } = getStatusInfo(permit.status);
                statusBadge.textContent = text;
                statusBadge.className = `px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full ${color}`;

                const aksiContainer = row.querySelector('[data-field="aksi-buttons"]');
                
                // ========================================================
                // --- INI ADALAH LOGIKA YANG DIPERBARUI ---
                // ========================================================
                
                if (permit.status === 1) { // Pending Checklist
                    
                    // Cek GWP (Relasi hasMany -> cek 'length')
                    // 'permit.permit_gwp' adalah array
                    if (permit.permit_gwp && permit.permit_gwp.length > 0) {
                        const gwp = permit.permit_gwp[0]; // Ambil data GWP pertama
                        aksiContainer.appendChild(
                            createAksiButton(
                                `{{ url('gwp-cek/view') }}/${gwp.id}`, 
                                'Isi Checklist GWP', 
                                'text-blue-600',
                                `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>`
                            )
                        );
                    }
                    
                    // Cek CSE (Relasi hasMany -> cek 'length')
                    if (permit.permit_cse && permit.permit_cse.length > 0) {
                        const cse = permit.permit_cse[0];
                        aksiContainer.appendChild(
                            createAksiButton(
                                `{{ url('cse-cek/view') }}/${cse.id}`, 
                                'Isi Checklist CSE', 
                                'text-purple-600',
                                `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2Z"></path><path d="m12 16-4-4 4-4"></path><path d="M16 12H8"></path></svg>`
                            )
                        );
                    }
                    
                    // [BARU] Cek HWP (Kerja Panas)
                    if (permit.permit_hwp && permit.permit_hwp.length > 0) {
                        const hwp = permit.permit_hwp[0];
                        aksiContainer.appendChild(
                            createAksiButton(
                                `{{ url('hwp-cek/view') }}/${hwp.id}`, 
                                'Isi Checklist HWP', 
                                'text-red-600', // Warna api
                                `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M14.5 4.5c.3-.3.6-.6.8-1 .4-1 1-1.5 2.2-1.5 1.6 0 2.5 1.2 2.5 3 0 .4-.1.7-.3 1.1-.2.5-.4 1-.6 1.4-.3.6-.6 1.1-1 1.5-.4.4-.8.8-1.3 1.1l-.1.1c-2 1.6-4.4 2.1-7.2 1.4C5.1 11.2 3 8.3 3 5c0-1.1.4-2.1 1.2-2.8C5 1.4 6.2 1 7.5 1c1.5 0 2.7 1 3.4 2.1.3.4.5.8.7 1.2"></path><path d="M15.5 7.5c.3-.3.6-.6.8-1 .4-1 1-1.5 2.2-1.5 1.6 0 2.5 1.2 2.5 3 0 .4-.1.7-.3 1.1-.2.5-.4 1-.6 1.4-.3.6-.6 1.1-1 1.5-.4.4-.8.8-1.3 1.1l-.1.1c-2 1.6-4.4 2.1-7.2 1.4C7.1 13.2 5 10.3 5 7c0-1.1.4-2.1 1.2-2.8C7 3.4 8.2 3 9.5 3c1.5 0 2.7 1 3.4 2.1.3.4.5.8.7 1.2"></path></svg>`
                            )
                        );
                    }

                    // [BARU] Cek EWP (Galian)
                    if (permit.permit_ewp && permit.permit_ewp.length > 0) {
                        const ewp = permit.permit_ewp[0];
                        aksiContainer.appendChild(
                            createAksiButton(
                                `{{ url('ewp-cek/view') }}/${ewp.id}`, 
                                'Isi Checklist EWP', 
                                'text-yellow-700', // Warna tanah
                                `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="m3 11 9-9 9 9v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"></path><path d="M9 21v-6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6"></path></svg>` // Ganti ikon jika perlu
                            )
                        );
                    }

                    // [BARU] Cek LP (Lifting)
                    if (permit.permit_lp && permit.permit_lp.length > 0) {
                        const lp = permit.permit_lp[0];
                        aksiContainer.appendChild(
                            createAksiButton(
                                `{{ url('lp-cek/view') }}/${lp.id}`, 
                                'Isi Checklist LP', 
                                'text-gray-600', // Warna crane
                                `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M18 9c0-1.7-1.3-3-3-3s-3 1.3-3 3 1.3 3 3 3 3-1.3 3-3Z"></path><path d="M12 6V5a3 3 0 0 0-3-3H7a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h2"></path><path d="M12 12L4 20"></path><path d="m15 9-1-1"></path><path d="M12 12v9"></path><path d="M12 21h4"></path></svg>`
                            )
                        );
                    }
                }
                
                // ========================================================
                // --- AKHIR DARI LOGIKA YANG DIPERBARUI ---
                // ========================================================
                
                if (permit.status === 3) { // Approved
                    aksiContainer.appendChild(
                        createAksiButton(
                            '#', 
                            'Tandai Pekerjaan Selesai', 
                            'text-green-600',
                            `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`,
                            'start-completion'
                        )
                    );
                }

                tableBody.appendChild(row);
            });

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
     * Memuat daftar user (Supervisor) untuk dropdown di modal
     */
    async function loadRequestFormOptions() {
        try {
            const response = await fetch(USER_API_URL, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (!response.ok) throw new Error('Gagal memuat daftar supervisor.');
            const result = await response.json();

            if (result.success) {
                const supervisors = result.data.filter(user => user.role === 'supervisor');
                supervisorSelect.innerHTML = '<option value="">Pilih Supervisor...</option>'; // Reset
                supervisors.forEach(user => {
                    const option = new Option(user.nama, user.id);
                    supervisorSelect.add(option);
                });
            }
        } catch (e) {
            console.error("Error loadRequestFormOptions:", e);
            supervisorSelect.innerHTML = '<option value="">Gagal memuat</option>';
        }
    }

    /**
     * Aksi: Menyimpan Pengajuan Pekerjaan Baru
     */
    async function handleSaveRequest() {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Mengirim...';
        
        // Reset error
        document.querySelectorAll('[id^="error_"]').forEach(el => el.classList.add('hidden'));

        const formData = new FormData(requestJobForm);

        try {
            const response = await fetch(REQUEST_JOB_URL, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    // Tampilkan error validasi
                    Object.keys(result.errors).forEach(key => {
                        const errorEl = document.getElementById(`error_${key}`);
                        if (errorEl) {
                            errorEl.textContent = result.errors[key][0];
                            errorEl.classList.remove('hidden');
                        }
                    });
                }
                throw new Error(result.message || 'Data yang Anda masukkan tidak valid.');
            }

            showToast(result.message, 'success');
            closeModal();
            loadPermits(); // Muat ulang tabel

        } catch (e) {
            showToast(e.message, 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Kirim Pengajuan';
        }
    }

    /**
     * Aksi: Memulai Alur Penutupan (Pekerjaan Selesai)
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
            loadPermits(); // Muat ulang tabel
            
        } catch (e) {
            showToast(e.message, 'error');
        }
    }

    // ===========================================
    // FUNGSI BANTU MODAL
    // ===========================================
    function openModal() {
        requestJobForm.reset();
        document.querySelectorAll('[id^="error_"]').forEach(el => el.classList.add('hidden'));
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-95');
        }, 10);
        
        loadRequestFormOptions();
    }

    function closeModal() {
        modal.classList.add('opacity-0');
        modal.querySelector('.transform').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // ===========================================
    // FUNGSI BANTU (HELPER)
    // ===========================================

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
    
    // ==========================================================
    // !!! INI PERBAIKANNYA (Menyebabkan error 'Indeks') !!!
    // ==========================================================
    window.showToast = (message, type = 'success') => {
        const toast = document.createElement('div');
        // [PERBAIKAN] Mengganti 'Indeks:' dengan ':'
        const bgColor = type === 'success' 
            ? 'bg-gradient-to-r from-green-500 to-emerald-600' 
            : 'bg-gradient-to-r from-red-500 to-rose-600'; // <-- TYPO DIPERBAIKI DI SINI
        
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
        // [DIUBAH] Tambahkan status 10
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
            case 10: return { text: 'Pending HSE Review', color: 'bg-cyan-200 text-cyan-800' }; 
            default: return { text: 'Unknown', color: 'bg-slate-200 text-slate-700' };
        }
    }

    // ===========================================
    // INISIALISASI
    // ===========================================
    document.addEventListener('DOMContentLoaded', () => {
        loadPermits(); // Muat tabel utama

        // Event listener untuk modal
        addJobBtn.addEventListener('click', openModal);
        closeModalBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        saveBtn.addEventListener('click', handleSaveRequest);
    });

</script>
@endpush