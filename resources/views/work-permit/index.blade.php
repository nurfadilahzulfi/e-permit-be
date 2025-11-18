@extends('layouts.app')

@section('title', 'Tinjauan Izin Kerja - E-Permit')
@section('page-title', 'Tinjauan Izin Kerja')

@section('content')

{{-- Wadah untuk notifikasi toast --}}
<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm"></div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">
        Daftar Pengajuan Pekerjaan (Status 10)
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
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Pemohon</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Pekerjaan</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Supervisor</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Aksi</th>
                </tr>
            </thead>
            
            <tbody id="tabel-review" class="divide-y divide-slate-200 dark:divide-slate-700">
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
                {{-- Template Baris Data Kosong --}}
                <template id="empty-row-template">
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                            <span class="text-lg font-medium">Tidak ada pengajuan pekerjaan yang perlu ditinjau.</span>
                        </td>
                    </tr>
                </template>
                {{-- Template Baris Data --}}
                <template id="review-row-template">
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-blue-600 dark:text-blue-400" data-field="nomor_pekerjaan"></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400" data-field="tgl_pekerjaan_dimulai"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            {{-- [PERBAIKAN] Data nama ada di sini --}}
                            <span data-field="nama_pemohon"></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-800 dark:text-slate-100 max-w-xs truncate" data-field="deskripsi_pekerjaan" title=""></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400" data-field="lokasi"></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                            {{-- [PERBAIKAN] Data nama ada di sini --}}
                            <span data-field="nama_supervisor"></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button data-action="review" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md transition-colors duration-200">
                                Review
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL UNTUK TINJAUAN HSE (Langkah 3) --}}
{{-- ========================================== --}}
{{-- ========================================== --}}
{{-- [VERSI UI BARU] MODAL TINJAUAN HSE (Langkah 3) --}}
{{-- ========================================== --}}
<div id="reviewModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 p-4">
    
    {{-- [PERUBAHAN] Mengganti 'grid' dengan 'flex' untuk layout scroll yang lebih baik --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full 
                max-w-lg md:max-w-2xl lg:max-w-4xl 
                max-h-[90vh] 
                flex flex-col {{-- <-- DIUBAH --}}
                transform scale-95 transition-transform duration-300
                border border-slate-200 dark:border-slate-700">
        
        {{-- Modal Header --}}
        <div class="flex-shrink-0 flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 dark:text-slate-100">Tinjau Izin Kerja (Langkah 3)</h3>
            <button id="closeModalBtn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        {{-- [PERUBAHAN] Body Modal kini memiliki wrapper untuk scrolling --}}
        <div class="flex-1 overflow-hidden">
            <form id="reviewForm" class="overflow-y-auto h-full p-6 space-y-6">
                @csrf
                <input type="hidden" id="work_permit_id" name="work_permit_id">

                {{-- Info dari Pemohon (Read Only) --}}
                {{-- [PERUBAHAN] Mengganti 'opacity-80' dengan style background --}}
                <fieldset class="space-y-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                    <legend class="px-2 text-lg font-bold text-blue-600 dark:text-blue-400">
                        Info dari Pemohon
                    </legend>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-500 dark:text-slate-400">Deskripsi Pekerjaan:</label>
                        <p id="info_deskripsi_pekerjaan" class="text-base font-medium text-slate-800 dark:text-slate-100 min-h-[1.5rem]"></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400">Lokasi:</label>
                            <p id="info_lokasi" class="text-base font-medium text-slate-800 dark:text-slate-100 min-h-[1.5rem]"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400">Pemohon:</label>
                            <p id="info_pemohon" class="text-base font-medium text-slate-800 dark:text-slate-100 min-h-[1.5rem]"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400">Supervisor:</label>
                            <p id="info_supervisor" class="text-base font-medium text-slate-800 dark:text-slate-100 min-h-[1.5rem]"></p>
                        </div>
                    </div>
                </fieldset>

                {{-- Form Isian HSE (JSA) --}}
                <fieldset class="space-y-5 p-4 border rounded-lg dark:border-slate-700">
                    <legend class="px-2 text-lg font-bold text-blue-600 dark:text-blue-400 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        1. Job Safety Analysis (JSA)
                    </legend>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label for="langkah_pekerjaan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Langkah Pekerjaan <span class="text-red-500">*</span></label>
                            {{-- [PERUBAHAN] Style input --}}
                            <textarea id="langkah_pekerjaan" name="langkah_pekerjaan" rows="4" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" required></textarea>
                            <span id="error_langkah_pekerjaan" class="text-xs text-red-500 mt-1 hidden"></span>
                        </div>
                        <div>
                            <label for="potensi_bahaya" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Potensi Bahaya <span class="text-red-500">*</span></label>
                            <textarea id="potensi_bahaya" name="potensi_bahaya" rows="4" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" required></textarea>
                            <span id="error_potensi_bahaya" class="text-xs text-red-500 mt-1 hidden"></span>
                        </div>
                        <div>
                            <label for="pengendalian_risiko" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Pengendalian Risiko <span class="text-red-500">*</span></label>
                            <textarea id="pengendalian_risiko" name="pengendalian_risiko" rows="4" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" required></textarea>
                            <span id="error_pengendalian_risiko" class="text-xs text-red-500 mt-1 hidden"></span>
                        </div>
                    </div>
                </fieldset>

                {{-- Form Isian HSE (Permit & Info Tambahan) --}}
                <fieldset x-data="{ requiresGWP: false, requiresCSE: false, requiresHWP: false, requiresEWP: false, requiresLP: false }" class="space-y-5 p-4 border rounded-lg dark:border-slate-700">
                    <legend class="px-2 text-lg font-bold text-blue-600 dark:text-blue-400 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="m9 14 2 2 4-4"></path></svg>
                        2. Persyaratan Izin (Permit)
                    </legend>
                    
                    <span id="error_permits_required" class="text-xs text-red-500 -mt-2 hidden"></span>
                    
                    {{-- [PERUBAHAN] Checkbox kini menggunakan 'peer' untuk styling --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <label class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border dark:border-slate-700 cursor-pointer transition-all duration-200 ring-2 ring-transparent hover:shadow peer-checked:ring-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30">
                            <input type="checkbox" id="req_gwp" name="permits_required[]" value="GWP" x-model="requiresGWP" class="w-5 h-5 text-blue-600 peer focus:ring-blue-500">
                            <span class="font-medium text-slate-800 dark:text-slate-100">GWP</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border dark:border-slate-700 cursor-pointer transition-all duration-200 ring-2 ring-transparent hover:shadow peer-checked:ring-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30">
                            <input type="checkbox" id="req_cse" name="permits_required[]" value="CSE" x-model="requiresCSE" class="w-5 h-5 text-indigo-600 peer focus:ring-indigo-500">
                            <span class="font-medium text-slate-800 dark:text-slate-100">CSE</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border dark:border-slate-700 cursor-pointer transition-all duration-200 ring-2 ring-transparent hover:shadow peer-checked:ring-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/30">
                            <input type="checkbox" id="req_hwp" name="permits_required[]" value="HWP" x-model="requiresHWP" class="w-5 h-5 text-red-600 peer focus:ring-red-500">
                            <span class="font-medium text-slate-800 dark:text-slate-100">HWP</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border dark:border-slate-700 cursor-pointer transition-all duration-200 ring-2 ring-transparent hover:shadow peer-checked:ring-yellow-500 peer-checked:bg-yellow-50 dark:peer-checked:bg-yellow-900/30">
                            <input type="checkbox" id="req_ewp" name="permits_required[]" value="EWP" x-model="requiresEWP" class="w-5 h-5 text-yellow-600 peer focus:ring-yellow-500">
                            <span class="font-medium text-slate-800 dark:text-slate-100">EWP</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border dark:border-slate-700 cursor-pointer transition-all duration-200 ring-2 ring-transparent hover:shadow peer-checked:ring-gray-500 peer-checked:bg-gray-50 dark:peer-checked:bg-gray-900/30">
                            <input type="checkbox" id="req_lp" name="permits_required[]" value="LP" x-model="requiresLP" class="w-5 h-5 text-gray-600 peer focus:ring-gray-500">
                            <span class="font-medium text-slate-800 dark:text-slate-100">Lifting (LP)</span>
                        </label>
                    </div>

                    {{-- Info Tambahan (Muncul jika permit dipilih) --}}
                    <div class="space-y-4 pt-4 border-t dark:border-slate-700">
                        <div x-show="requiresGWP" x-transition>
                            <label for="peralatan_pekerjaan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Peralatan (GWP) <span class="text-red-500">*</span></label>
                            <input type="text" id="peralatan_pekerjaan" name="peralatan_pekerjaan" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" placeholder="Misal: Gerinda, Bor, ...">
                        </div>
                        <div x-show="requiresCSE" x-transition class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="gas_tester_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Gas Tester (CSE) <span class="text-red-500">*</span></label>
                                <input type="text" id="gas_tester_name" name="gas_tester_name" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="entry_supervisor_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Entry Supervisor (CSE) <span class="text-red-500">*</span></label>
                                <input type="text" id="entry_supervisor_name" name="entry_supervisor_name" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div x-show="requiresHWP" x-transition>
                            <label for="equipment_tools" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Equipment/Tools (HWP) <span class="text-red-500">*</span></label>
                            <input type="text" id="equipment_tools" name="equipment_tools" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" placeholder="Misal: Mesin Las, ...">
                        </div>
                        <div x-show="requiresEWP" x-transition class="space-y-4">
                            <div>
                                <label for="kedalaman_galian_meter" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Perkiraan Kedalaman Galian (meter) <span class="text-red-500">*</span></label>
                                <input type="text" id="kedalaman_galian_meter" name="kedalaman_galian_meter" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" placeholder="Misal: 1.5 meter">
                            </div>
                        </div>
                        <div x-show="requiresLP" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="crane_capacity" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kapasitas Crane <span class="text-red-500">*</span></label>
                                <input type="text" id="crane_capacity" name="crane_capacity" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" placeholder="Misal: 5 Ton">
                            </div>
                            <div>
                                <label for="load_weight" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Berat Beban (Ton) <span class="text-red-500">*</span></label>
                                <input type="text" id="load_weight" name="load_weight" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" placeholder="Misal: 2 Ton">
                            </div>
                        </div>
                    </div>
                </fieldset>

                {{-- Form Isian HSE (Waktu) --}}
                <fieldset class="space-y-5 p-4 border rounded-lg dark:border-slate-700">
                    <legend class="px-2 text-lg font-bold text-blue-600 dark:text-blue-400 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        3. Waktu Pelaksanaan
                    </legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="shift_kerja" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Shift Kerja <span class="text-red-500">*</span></label>
                            <select id="shift_kerja" name="shift_kerja" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Shift...</option>
                                <option value="Pagi">Pagi (08:00 - 17:00)</option>
                                <option value="Malam">Malam (20:00 - 05:00)</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <span id="error_shift_kerja" class="text-xs text-red-500 mt-1 hidden"></span>
                        </div>
                        <div>
                            <label for="tgl_pekerjaan_selesai" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Selesai Tgl <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="tgl_pekerjaan_selesai" name="tgl_pekerjaan_selesai" class="w-full px-4 py-2 rounded-lg bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-600 focus:ring-2 focus:ring-blue-500" required>
                            <span id="error_tgl_pekerjaan_selesai" class="text-xs text-red-500 mt-1 hidden"></span>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>

        {{-- Modal Footer --}}
        <div class="flex-shrink-0 flex items-center justify-end p-5 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 rounded-b-2xl">
            <button id="cancelReviewBtn" class="px-5 py-2.5 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100 text-sm font-medium mr-3 hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                Batal
            </button>
            <button id="saveReviewBtn" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md transition-colors">
                Simpan & Kirim ke Pemohon
            </button>
        </div>
    </div>
</div>

@endsection

{{-- ========================================================== --}}
{{-- [PERBAIKAN] Seluruh blok <script> di bawah ini diperbarui --}}
{{-- ========================================================== --}}
@push('scripts')
<script>
    // URL API
    const API_URL = "{{ route('work-permit.hse-review-list') }}";
    const REVIEW_URL_BASE = "{{ url('work-permit') }}"; // -> /work-permit/{id}/review-assign
    
    // Elemen Global
    const toastContainer = document.getElementById('toast-container');
    const tableBody = document.getElementById('tabel-review');
    const loadingRow = document.getElementById('loading-row');
    const reviewRowTemplate = document.getElementById('review-row-template');
    const emptyRowTemplate = document.getElementById('empty-row-template');
    
    // Elemen Modal
    const modal = document.getElementById('reviewModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelReviewBtn');
    const saveBtn = document.getElementById('saveReviewBtn');
    const reviewForm = document.getElementById('reviewForm');
    const workPermitIdField = document.getElementById('work_permit_id');

    // Data global untuk menyimpan list
    let permitList = [];

    // ===========================================
    // FUNGSI UTAMA (LOAD & SAVE)
    // ===========================================

    async function loadReviews() {
        loadingRow.classList.remove('hidden');
        tableBody.querySelectorAll('tr:not(#loading-row)').forEach(row => row.remove());

        try {
            const response = await fetch(API_URL, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            if (!response.ok) throw new Error('Gagal memuat data tinjauan.');
            const result = await response.json();

            loadingRow.classList.add('hidden');
            if (!result.success || result.data.length === 0) {
                tableBody.appendChild(emptyRowTemplate.content.cloneNode(true));
                permitList = [];
                return;
            }

            permitList = result.data; // Simpan data ke global
            tableBody.innerHTML = ''; // Hapus loading/empty

            permitList.forEach(permit => {
                const row = reviewRowTemplate.content.cloneNode(true);
                
                // [PERBAIKAN] Menggunakan relasi objek 'pemohon' dan 'supervisor'
                row.querySelector('[data-field="nomor_pekerjaan"]').textContent = permit.nomor_pekerjaan;
                row.querySelector('[data-field="tgl_pekerjaan_dimulai"]').textContent = `Mulai: ${formatTanggal(permit.tgl_pekerjaan_dimulai)}`;
                
                // Di sini perbaikannya:
                row.querySelector('[data-field="nama_pemohon"]').textContent = permit.pemohon ? permit.pemohon.nama : 'N/A';
                
                row.querySelector('[data-field="deskripsi_pekerjaan"]').textContent = permit.deskripsi_pekerjaan;
                row.querySelector('[data-field="deskripsi_pekerjaan"]').title = permit.deskripsi_pekerjaan;
                row.querySelector('[data-field="lokasi"]').textContent = permit.lokasi;
                
                // Di sini perbaikannya:
                row.querySelector('[data-field="nama_supervisor"]').textContent = permit.supervisor ? permit.supervisor.nama : 'N/A';
                
                // Event Listeners
                row.querySelector('[data-action="review"]').addEventListener('click', () => openModal(permit));

                tableBody.appendChild(row);
            });

        } catch (e) {
            loadingRow.classList.add('hidden');
            console.error("Error loadReviews:", e);
            showToast(e.message, 'error');
        }
    }

    async function handleSaveReview() {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Menyimpan...';
        const id = workPermitIdField.value;

        // Reset error
        document.querySelectorAll('[id^="error_"]').forEach(el => el.classList.add('hidden'));

        const formData = new FormData(reviewForm);
        const url = `${REVIEW_URL_BASE}/${id}/review-assign`;

        try {
            const response = await fetch(url, {
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
                    Object.keys(result.errors).forEach(key => {
                        const errorEl = document.getElementById(`error_${key.split('.')[0]}`); // Handle array errors
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
            loadReviews(); // Muat ulang tabel

        } catch (e) {
            showToast(e.message, 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Simpan & Kirim ke Pemohon';
        }
    }

    // ===========================================
    // FUNGSI BANTU (MODAL & HELPER)
    // ===========================================
    function openModal(permit) { // [PERBAIKAN] Menerima 'permit'
        reviewForm.reset();
        
        // Hapus error spans jika ada
        const errorSpans = reviewForm.querySelectorAll('span[id^="error_"]');
        if (errorSpans.length > 0) {
            errorSpans.forEach(el => el.classList.add('hidden'));
        }

        // Isi ID
        workPermitIdField.value = permit.id;

        // [PERBAIKAN] Menggunakan relasi objek 'pemohon' dan 'supervisor'
        document.getElementById('info_deskripsi_pekerjaan').textContent = permit.deskripsi_pekerjaan;
        document.getElementById('info_lokasi').textContent = permit.lokasi;
        
        // Di sini perbaikannya:
        document.getElementById('info_pemohon').textContent = permit.pemohon ? permit.pemohon.nama : 'N/A';
        document.getElementById('info_supervisor').textContent = permit.supervisor ? permit.supervisor.nama : 'N/A';
        
        // Set tanggal selesai (agar tidak error 'after')
        // Format YYYY-MM-DDTHH:MM
        const tglMulai = permit.tgl_pekerjaan_dimulai.split(' ')[0];
        document.getElementById('tgl_pekerjaan_selesai').value = `${tglMulai}T00:00`;

        // Buka modal
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-95');
        }, 10);
    }

    function closeModal() {
        modal.classList.add('opacity-0');
        modal.querySelector('.transform').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    window.showToast = (message, type = 'success') => {
        const toast = document.createElement('div');
        // [PERBAIKAN] Typo 'Indeks:' dihapus
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

    // ===========================================
    // INISIALISASI
    // ===========================================
    document.addEventListener('DOMContentLoaded', () => {
        loadReviews(); // Muat tabel utama

        // Event listener untuk modal
        closeModalBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        saveBtn.addEventListener('click', handleSaveReview);
    });

</script>
@endpush