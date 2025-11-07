@extends('layouts.app')

{{-- DIUBAH: Title untuk halaman Permit GWP --}}
@section('title', 'Manajemen Permit GWP - E-Permit')
@section('page-title', 'Manajemen Permit GWP')

@section('content')

{{-- Wadah untuk notifikasi toast --}}
<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm">
    {{-- Toast akan muncul di sini --}}
</div>

{{-- Header Halaman --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">
        Daftar Izin GWP Saya
    </h2>
    <button id="addGwpBtn"
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg transition duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        <span>Ajukan Izin GWP Baru</span>
    </button>
</div>

{{-- Pesan untuk mobile --}}
<div class="sm:hidden text-sm text-slate-500 dark:text-slate-400 mb-2">
    <span class="font-bold">→</span> Geser tabel ke samping untuk melihat semua kolom.
</div>

{{-- Container Tabel --}}
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            
            {{-- Header Tabel --}}
            <thead class="bg-slate-50 dark:bg-slate-900/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">No. Izin</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Pekerjaan</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Pemohon</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Supervisor</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Aksi</th>
                </tr>
            </thead>
            
            {{-- Body Tabel --}}
            <tbody id="tabel-gwp" class="divide-y divide-slate-200 dark:divide-slate-700">
                
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
                                <span class="text-lg font-medium">Anda belum pernah mengajukan izin GWP.</span>
                                <span class="text-sm">Klik tombol "Ajukan Izin GWP Baru" untuk memulai.</span>
                            </div>
                        </td>
                    </tr>
                </template>

                {{-- Baris Data Izin (Template) --}}
                <template id="permit-row-template">
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        {{-- Data No. Izin --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-blue-600 dark:text-blue-400" data-field="nomor_izin"></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400" data-field="tgl_permohonan"></div>
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
                        {{-- Data Supervisor --}}
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
                            <div class="flex items-center gap-2">
                                {{-- Tombol Detail/Checklist --}}
                                <a href="#" data-action="detail" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors px-2 py-1.5 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/50" title="Lihat Detail & Checklist">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>
                                </a>
                                {{-- Tombol Hapus --}}
                                <button data-action="delete" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors px-2 py-1.5 rounded-md hover:bg-red-100 dark:hover:bg-red-900/50" title="Hapus Izin">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

            </tbody>
        </table>
    </div>
</div>


{{-- ========================================== --}}
{{-- MODAL UNTUK TAMBAH/EDIT GWP --}}
{{-- ========================================== --}}
<div id="gwpModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col transform scale-95 transition-transform duration-300">
        
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 dark:text-slate-100">Ajukan Izin GWP Baru</h3>
            <button id="closeModalBtn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        {{-- Modal Body (Form) --}}
        <form id="gwpForm" class="flex-1 overflow-y-auto p-6 space-y-5">
            @csrf
            <input type="hidden" id="gwp_id" name="gwp_id"> {{-- Untuk mode edit --}}

            {{-- 
            ===================================================
            PERBAIKAN #1: NOMOR IZIN (READONLY)
            PERBAIKAN #3: NAMA KOLOM (nomor_izin -> nomor)
            ===================================================
            --}}
            <div>
                <label for="nomor" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nomor Izin</label>
                <input type="text" id="nomor" name="nomor" 
                       class="w-full px-4 py-2 rounded-lg bg-slate-200 dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 cursor-not-allowed" 
                       placeholder="Akan digenerate otomatis"
                       readonly>
                <span id="error_nomor" class="text-xs text-red-500 mt-1 hidden"></span>
            </div>

            {{-- 
            ===================================================
            PERBAIKAN #2: FIELD JENIS IZIN (DIHAPUS)
            ===================================================
            --}}
            {{-- Field Jenis Izin telah dihapus --}}

            {{-- Grid 2 Kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Supervisor --}}
                <div>
                    <label for="supervisor_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Supervisor Pemilik Lokasi <span class="text-red-500">*</span></label>
                    <select id="supervisor_id" name="supervisor_id" class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" required>
                        <option value="">Pilih Supervisor...</option>
                        {{-- Opsi Supervisor akan diisi oleh JS --}}
                    </select>
                    <span id="error_supervisor_id" class="text-xs text-red-500 mt-1 hidden"></span>
                </div>
                
                {{-- Shift Kerja --}}
                <div>
                    <label for="shift_kerja" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Shift Kerja <span class="text-red-500">*</span></label>
                    <select id="shift_kerja" name="shift_kerja" class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" required>
                        <option value="">Pilih Shift...</option>
                        <option value="Pagi">Pagi (08:00 - 17:00)</option>
                        <option value="Malam">Malam (20:00 - 05:00)</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <span id="error_shift_kerja" class="text-xs text-red-500 mt-1 hidden"></span>
                </div>
            </div>

            {{-- 
            ===================================================
            PERBAIKAN #3: NAMA KOLOM (lokasi_pekerjaan -> lokasi)
            ===================================================
            --}}
            <div>
                <label for="lokasi" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Lokasi Pekerjaan <span class="text-red-500">*</span></label>
                <input type="text" id="lokasi" name="lokasi" 
                       class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                       placeholder="Misal: Area Gedung A, Lantai 2" required>
                <span id="error_lokasi" class="text-xs text-red-500 mt-1 hidden"></span>
            </div>

            {{-- Deskripsi Pekerjaan --}}
            <div>
                <label for="deskripsi_pekerjaan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                <textarea id="deskripsi_pekerjaan" name="deskripsi_pekerjaan" rows="3" 
                          class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                          placeholder="Jelaskan pekerjaan yang akan dilakukan..." required></textarea>
                <span id="error_deskripsi_pekerjaan" class="text-xs text-red-500 mt-1 hidden"></span>
            </div>

            {{-- Peralatan Pekerjaan --}}
            <div>
                <label for="peralatan_pekerjaan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Peralatan yang Digunakan <span class="text-red-500">*</span></label>
                <textarea id="peralatan_pekerjaan" name="peralatan_pekerjaan" rows="3" 
                          class="w-full px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-500 dark:focus:ring-blue-500 transition duration-200" 
                          placeholder="Sebutkan semua alat, misal: Gerinda, Mesin Las, Bor..." required></textarea>
                <span id="error_peralatan_pekerjaan" class="text-xs text-red-500 mt-1 hidden"></span>
            </div>

        </form>

        {{-- Modal Footer --}}
        <div class="flex items-center justify-end p-5 border-t border-slate-200 dark:border-slate-700">
            <button id="cancelBtn" 
                    class="px-5 py-2.5 rounded-lg bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-100 text-sm font-medium transition-colors duration-200 mr-3">
                Batal
            </button>
            <button id="saveBtn" 
                    class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                Simpan Permohonan
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // URL API
    const API_URL = "{{ url('permit-gwp') }}";
    const USER_API_URL = "{{ url('user') }}"; // Untuk ambil daftar supervisor
    
    // Elemen Global
    const toastContainer = document.getElementById('toast-container');
    const tableBody = document.getElementById('tabel-gwp');
    const loadingRow = document.getElementById('loading-row');
    const permitRowTemplate = document.getElementById('permit-row-template');
    const emptyRowTemplate = document.getElementById('empty-row-template');
    
    // Elemen Modal
    const modal = document.getElementById('gwpModal');
    const modalTitle = document.getElementById('modalTitle');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const addGwpBtn = document.getElementById('addGwpBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveBtn = document.getElementById('saveBtn');
    const gwpForm = document.getElementById('gwpForm');
    
    // Elemen Form
    const gwpIdField = document.getElementById('gwp_id');
    const supervisorSelect = document.getElementById('supervisor_id');
    // ... (elemen form lain bisa ditambahkan di sini jika perlu) ...


    // ===========================================
    // FUNGSI UTAMA (CRUD & ACTIONS)
    // ===========================================

    /**
     * Memuat daftar izin GWP dari API
     */
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
                // [PERBAIKAN] Sesuaikan dengan nama kolom dari DB (nomor)
                row.querySelector('[data-field="nomor_izin"]').textContent = permit.nomor; 
                row.querySelector('[data-field="tgl_permohonan"]').textContent = formatTanggal(permit.tgl_permohonan);
                row.querySelector('[data-field="deskripsi_pekerjaan"]').textContent = permit.deskripsi_pekerjaan;
                row.querySelector('[data-field="deskripsi_pekerjaan"]').title = permit.deskripsi_pekerjaan;
                // [PERBAIKAN] Sesuaikan dengan nama kolom dari DB (lokasi)
                row.querySelector('[data-field="lokasi_pekerjaan"]').textContent = permit.lokasi;
                row.querySelector('[data-field="nama_pemohon"]').textContent = permit.pemohon ? permit.pemohon.nama : 'N/A';
                row.querySelector('[data-field="nama_supervisor"]').textContent = permit.supervisor ? permit.supervisor.nama : 'N/A';
                
                // Status Badge
                const statusBadge = row.querySelector('[data-field="status_badge"]');
                const { text, color } = getStatusInfo(permit.status);
                statusBadge.textContent = text;
                statusBadge.className = `px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full ${color}`;

                // Event Listeners untuk Tombol Aksi
                const detailButton = row.querySelector('[data-action="detail"]');
                const deleteButton = row.querySelector('[data-action="delete"]');

                // Arahkan tombol detail ke halaman checklist
                detailButton.href = `{{ url('gwp-cek/view') }}/${permit.id}`;
                
                deleteButton.addEventListener('click', () => handleDelete(permit.id));
                
                // Sembunyikan tombol hapus jika status bukan DRAFT (misal 0) atau REJECTED (4)
                if (permit.status !== 0 && permit.status !== 4) {
                    deleteButton.remove();
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
     * Memuat daftar user (Supervisor) untuk dropdown di modal
     */
    async function loadFormOptions() {
        try {
            // Ambil data User
            const response = await fetch(USER_API_URL, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (!response.ok) throw new Error('Gagal memuat daftar user.');
            const result = await response.json();

            if (result.success) {
                // Filter hanya untuk supervisor
                const supervisors = result.data.filter(user => user.role === 'supervisor');
                supervisorSelect.innerHTML = '<option value="">Pilih Supervisor...</option>'; // Reset
                supervisors.forEach(user => {
                    const option = new Option(user.nama, user.id);
                    supervisorSelect.add(option);
                });
            }

            // (Bisa tambahkan load Jenis Izin di sini jika diperlukan lagi)
            
        } catch (e) {
            console.error("Error loadFormOptions:", e);
            showToast(e.message, 'error');
        }
    }

    /**
     * Menangani penyimpanan data (Tambah Baru atau Update)
     */
    async function handleSave() {
        // Reset error
        document.querySelectorAll('[id^="error_"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });

        const formData = new FormData(gwpForm);
        const data = Object.fromEntries(formData.entries());
        const id = gwpIdField.value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${API_URL}/${id}` : API_URL;

        // Tambahkan _method untuk PUT
        if(method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        // Kirim data menggunakan FormData (bukan JSON)
        try {
            const response = await fetch(url, {
                method: 'POST', // Laravel menangani PUT/PATCH via POST + _method
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
                    displayValidationErrors(result.errors);
                    throw new Error('Data yang Anda masukkan tidak valid.');
                }
                throw new Error(result.error || result.message || 'Terjadi kesalahan server.');
            }

            showToast(result.message, 'success');
            closeModal();
            loadPermits(); // Muat ulang tabel

        } catch (e) {
            showToast(e.message, 'error');
        }
    }
    
    /**
     * Menangani Hapus Data
     */
    async function handleDelete(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus izin ini? Aksi ini tidak dapat dibatalkan.')) {
            return;
        }
        
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('Izin berhasil dihapus.', 'success'); 
                loadPermits(); // Muat ulang tabel
            } else {
                throw new Error(data.message || 'Gagal menghapus izin.');
            }
        } catch (e) { 
            showToast(e.message, 'error'); 
        }
    }

    // ===========================================
    // FUNGSI BANTU (HELPER)
    // ===========================================

    /**
     * Buka Modal
     */
    function openModal() {
        gwpForm.reset();
        gwpIdField.value = '';
        modalTitle.textContent = 'Ajukan Izin GWP Baru';
        // Reset error
        document.querySelectorAll('[id^="error_"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-95');
        }, 10);
    }

    /**
     * Tutup Modal
     */
    function closeModal() {
        modal.classList.add('opacity-0');
        modal.querySelector('.transform').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300); // Sesuaikan dengan durasi transisi
    }

    /**
     * Tampilkan error validasi di bawah field
     */
    function displayValidationErrors(errors) {
        for (const [key, messages] of Object.entries(errors)) {
            const errorElement = document.getElementById(`error_${key}`);
            if (errorElement) {
                errorElement.textContent = messages.join(' ');
                errorElement.classList.remove('hidden');
            }
        }
    }

    /**
     * Konversi tanggal ke format "12 Okt 2024, 14:30"
     */
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

    /**
     * Konversi status ID ke Teks dan Warna
     */
    function getStatusInfo(status) {
        // Status: 0=Draft, 1=Pending SPV, 2=Pending HSE, 3=Approved, 4=Rejected, 5=Closed
        switch (Number(status)) {
            case 0: return { text: 'Draft', color: 'bg-slate-200 text-slate-700' };
            case 1: return { text: 'Pending SPV', color: 'bg-yellow-200 text-yellow-800' };
            case 2: return { text: 'Pending HSE', color: 'bg-yellow-200 text-yellow-800' };
            case 3: return { text: 'Approved', color: 'bg-green-200 text-green-800' };
            case 4: return { text: 'Rejected', color: 'bg-red-200 text-red-800' };
            case 5: return { text: 'Closed', color: 'bg-blue-200 text-blue-800' };
            default: return { text: 'Unknown', color: 'bg-slate-200 text-slate-700' };
        }
    }

    /**
     * FUNGSI NOTIFIKASI MODERN (Toast)
     */
    window.showToast = (message, type = 'success') => {
        const toast = document.createElement('div');
        const bgColor = type === 'success' 
            ? 'bg-gradient-to-r from-green-500 to-emerald-600' 
            : 'bg-gradient-to-r from-red-500 to-rose-600';
        
        toast.className = `
            px-5 py-3 rounded-xl shadow-xl text-white text-sm font-medium flex items-center gap-3
            ${bgColor}
            opacity-0 translate-x-10 transition-all duration-300 ease-out
        `;
        
        const icon = type === 'success' ? '✅' : '⚠️';
        toast.innerHTML = `<span class="text-lg">${icon}</span><div>${message}</div>`;
        
        toastContainer.appendChild(toast);
        
        // Animasi masuk
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-x-10');
            toast.classList.add('opacity-100', 'translate-x-0');
        }, 10);
        
        // Animasi keluar
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-10');
            toast.addEventListener('transitionend', () => toast.remove());
        }, 5000); // Hilang setelah 5 detik
    };


    // ===========================================
    // INISIALISASI & EVENT LISTENERS
    // ===========================================
    document.addEventListener('DOMContentLoaded', () => {
        loadPermits();
        loadFormOptions();

        // Tombol Buka Modal
        addGwpBtn.addEventListener('click', openModal);
        
        // Tombol Tutup Modal
        closeModalBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        
        // Tombol Simpan
        saveBtn.addEventListener('click', handleSave);
        gwpForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Mencegah submit form tradisional
            handleSave();
        });
    });

</script>
@endpush