@extends('layouts.app')

@section('title', 'Checklist Izin HWP')
@section('page-title', 'Checklist HWP: ' . $workPermit->nomor_pekerjaan)

@push('styles')
{{-- Style khusus untuk toggle --}}
<style>
    .toggle-checkbox:checked {
        right: 0;
        border-color: #ef4444; /* red-600 */
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #ef4444; /* red-600 */
    }
</style>
@endpush

@section('content')

{{-- Wadah untuk notifikasi toast --}}
<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm"></div>

{{-- Header Halaman --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div class="flex-1">
        <p class="text-sm text-slate-500 dark:text-slate-400">Pekerjaan:</p>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">
            {{ $workPermit->deskripsi_pekerjaan }}
        </h2>
        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
            <span class="font-medium">Lokasi:</span> {{ $workPermit->lokasi }} | 
            <span class="font-medium">Pemohon:</span> {{ $workPermit->pemohon->nama }}
        </p>
    </div>
    <div class="flex-shrink-0 flex items-center gap-3">
        <a href="{{ route('dashboard.my-permits') }}" 
           class="px-5 py-2.5 rounded-lg bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-100 text-sm font-medium transition-colors duration-200">
            Kembali
        </a>
        
        {{-- Tombol Kirim Persetujuan (Sama untuk semua permit) --}}
        @if($workPermit->status === 1 && Auth::id() === $workPermit->pemohon_id)
        <button id="submitApprovalBtn" 
                data-url="{{ route('work-permit.submit-approval', $workPermit->id) }}"
                class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
            Kirim untuk Persetujuan
        </button>
        
        @elseif($workPermit->status >= 2)
        <button class="px-5 py-2.5 rounded-lg bg-green-600 text-white text-sm font-medium shadow-md cursor-not-allowed" disabled>
            Sudah Terkirim
        </button>
        @endif
    </div>
</div>

{{-- Tampilkan Detail JSA --}}
<div class="mb-6 bg-white dark:bg-slate-800 rounded-xl shadow-xl ring-1 ring-slate-200 dark:ring-slate-700">
    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
        <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100">Job Safety Analysis (JSA)</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Harap tinjau JSA berikut sebelum memulai pekerjaan dan mengisi checklist.
        </p>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <h4 class="text-sm font-bold uppercase text-slate-500 dark:text-slate-400 mb-2">Langkah Pekerjaan</h4>
            <div class="prose prose-sm dark:prose-invert max-w-none">
                {!! nl2br(e($workPermit->langkah_pekerjaan)) !!}
            </div>
        </div>
        <div>
            <h4 class="text-sm font-bold uppercase text-slate-500 dark:text-slate-400 mb-2">Potensi Bahaya</h4>
            <div class="prose prose-sm dark:prose-invert max-w-none">
                {!! nl2br(e($workPermit->potensi_bahaya)) !!}
            </div>
        </div>
        <div>
            <h4 class="text-sm font-bold uppercase text-slate-500 dark:text-slate-400 mb-2">Pengendalian Risiko</h4>
            <div class="prose prose-sm dark:prose-invert max-w-none">
                {!! nl2br(e($workPermit->pengendalian_risiko)) !!}
            </div>
        </div>
    </div>
</div>

{{-- Container Checklist (Disederhanakan jadi 1 kolom) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    {{-- Kolom 1: Checklist HWP --}}
    <div class="lg:col-span-1" id="checklist-hwp-container">
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-3 p-3 bg-slate-100 dark:bg-slate-800 rounded-t-lg border-b-2 border-red-500">
            Checklist Kerja Panas (HWP)
        </h3>
        <div class="space-y-4 p-4 bg-white dark:bg-slate-800 rounded-b-lg shadow-xl ring-1 ring-slate-200 dark:ring-slate-700">
            {{-- Loading state --}}
            <div id="loading-hwp">Memuat checklist...</div>
            {{-- Konten diisi oleh JS --}}
        </div>
    </div>

    {{-- Kolom 2: Kosong (bisa diisi info lain) --}}
    <div class="lg:col-span-1">
         <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-3 p-3 bg-slate-100 dark:bg-slate-800 rounded-t-lg border-b-2 border-slate-500">
            Informasi Tambahan
        </h3>
        <div class="p-4 bg-white dark:bg-slate-800 rounded-b-lg shadow-xl ring-1 ring-slate-200 dark:ring-slate-700">
             <p class="text-slate-600 dark:text-slate-300">Pastikan semua item di checklist HWP telah terpenuhi sebelum mengirimkan untuk persetujuan.</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // URL API
    const LOAD_URL = "{{ route('hwp-cek.index', $permitHwp->id) }}";
    const UPDATE_URL_BASE = "{{ url('hwp-cek') }}"; // -> /hwp-cek/{id}
    
    // Elemen Global
    const toastContainer = document.getElementById('toast-container');
    const submitApprovalBtn = document.getElementById('submitApprovalBtn');

    // Hak akses
    const canEditPemohon = {{ ($workPermit->status === 1 && Auth::id() === $workPermit->pemohon_id) ? 'true' : 'false' }};
    const canEditHse = {{ ($workPermit->status === 2 && Auth::user()->role === 'hse') ? 'true' : 'false' }};
    
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

    /**
     * Membuat satu baris checklist item
     */
    function createChecklistItem(item, canEdit) {
        const id = `check_${item.id}`;
        const container = document.createElement('div');
        container.className = 'flex items-center justify-between';
        
        const label = document.createElement('label');
        label.htmlFor = id;
        label.className = 'flex-1 text-sm font-medium text-slate-700 dark:text-slate-300 cursor-pointer';
        label.textContent = item.nama;

        const toggleContainer = document.createElement('div');
        toggleContainer.className = 'relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = id;
        checkbox.checked = item.value;
        checkbox.dataset.id = item.id;
        checkbox.className = 'toggle-checkbox absolute block w-6 h-6 rounded-full bg-white dark:bg-slate-400 border-4 border-slate-300 dark:border-slate-500 appearance-none cursor-pointer transition-all duration-300';
        
        if (!canEdit) {
            checkbox.disabled = true;
            checkbox.classList.add('cursor-not-allowed');
            label.classList.add('cursor-not-allowed', 'opacity-70');
        }

        const toggleLabel = document.createElement('label');
        toggleLabel.htmlFor = id;
        toggleLabel.className = 'toggle-label block overflow-hidden h-6 rounded-full bg-slate-300 dark:bg-slate-500 cursor-pointer';

        toggleContainer.appendChild(checkbox);
        toggleContainer.appendChild(toggleLabel);
        
        container.appendChild(label);
        container.appendChild(toggleContainer);

        if (canEdit) {
            checkbox.addEventListener('change', handleCheckChange);
        }

        return container;
    }

    /**
     * Render semua item ke dalam container
     */
    function renderChecklist(containerId, loadingId, items, canEdit) {
        const container = document.querySelector(`#${containerId} div`);
        const loading = document.getElementById(loadingId);
        
        container.innerHTML = ''; // Kosongkan
        if (items.length === 0) {
            container.textContent = 'Tidak ada item checklist.';
            loading.classList.add('hidden');
            return;
        }
        
        items.forEach(item => {
            container.appendChild(createChecklistItem(item, canEdit));
        });
        loading.classList.add('hidden');
    }

    // ===========================================
    // FUNGSI UTAMA (LOAD & SAVE)
    // ===========================================

    /**
     * Memuat semua data checklist dari API
     */
    async function loadChecklists() {
        try {
            const response = await fetch(LOAD_URL, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (!response.ok) throw new Error(`Gagal memuat checklist. Status: ${response.status}`);
            
            const result = await response.json();
            if (!result.success) throw new Error(result.error || 'Gagal memuat data.');

            // Render grup (sesuaikan 'result.data.hwp_items' dengan key dari Controller)
            renderChecklist('checklist-hwp-container', 'loading-hwp', result.data.hwp_items, canEditPemohon);
            
            // (Note: 'canEditHse' bisa dipakai jika ada checklist khusus HSE)

        } catch (e) {
            console.error("Error loadChecklists:", e);
            showToast(e.message, 'error');
        }
    }

    /**
     * Menangani perubahan (check/uncheck) pada satu item
     */
    async function handleCheckChange(e) {
        const checkbox = e.target;
        const id = checkbox.dataset.id;
        const value = checkbox.checked;

        checkbox.disabled = true; 
        
        try {
            const response = await fetch(`${UPDATE_URL_BASE}/${id}`, {
                method: 'PUT',
                body: JSON.stringify({ value: value }),
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            if (!result.success) throw new Error(result.error || 'Gagal menyimpan.');
            
        } catch (e) {
            console.error("Error handleCheckChange:", e);
            showToast(e.message, 'error');
            checkbox.checked = !value; 
        } finally {
            checkbox.disabled = false;
        }
    }

    /**
     * Menangani klik tombol "Kirim Persetujuan"
     */
    async function handleSubmitApproval() {
        if (!confirm('Apakah Anda yakin sudah melengkapi semua checklist dan ingin mengirimkannya untuk persetujuan?')) {
            return;
        }
        
        const url = submitApprovalBtn.dataset.url;
        submitApprovalBtn.disabled = true;
        submitApprovalBtn.innerHTML = 'Mengirim...';
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            if (!result.success) throw new Error(result.error || 'Gagal mengirim.');
            
            showToast(result.message, 'success');
            
            submitApprovalBtn.innerHTML = 'Sudah Terkirim';
            submitApprovalBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            submitApprovalBtn.classList.add('bg-green-600', 'cursor-not-allowed');
            
            setTimeout(() => {
                window.location.href = "{{ route('dashboard.my-permits') }}";
            }, 2000);

        } catch (e) {
            console.error("Error handleSubmitApproval:", e);
            showToast(e.message, 'error');
            submitApprovalBtn.disabled = false;
            submitApprovalBtn.innerHTML = 'Kirim untuk Persetujuan';
        }
    }

    // ===========================================
    // INISIALISASI
    // ===========================================
    document.addEventListener('DOMContentLoaded', () => {
        loadChecklists();
        
        if (submitApprovalBtn) {
            submitApprovalBtn.addEventListener('click', handleSubmitApproval);
        }
    });

</script>
@endpush