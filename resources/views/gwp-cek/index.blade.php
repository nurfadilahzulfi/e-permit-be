@extends('layouts.app')

{{-- Mengambil nomor izin dari controller --}}
@section('title', 'Checklist Izin ' . $permit->nomor)
@section('page-title', 'Checklist Izin ' . $permit->nomor)

@section('content')

<!-- KONTENER TOAST (Untuk Notifikasi) -->
<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm">
    <!-- Notifikasi akan muncul di sini -->
</div>

<!-- HEADER HALAMAN -->
<div class="mb-6">
    <p class="text-slate-500 dark:text-slate-400">
        Harap lengkapi semua checklist verifikasi di bawah ini.
    </p>
</div>

<!-- KONTENER UNTUK CHECKLIST -->
{{-- 
    JavaScript akan mengisi 3 div ini dengan 'card' checklist
    berdasarkan data JSON yang diambil.
--}}
<div id="checklist-container" class="space-y-6">
    
    {{-- 1. Checklist untuk Pemohon --}}
    <div id="checklist-pemohon"></div>
    
    {{-- 2. Checklist untuk HSE --}}
    <div id="checklist-hse"></div>
    
    {{-- 3. Checklist untuk Alat --}}
    <div id="checklist-alat"></div>

    {{-- Loading Indicator --}}
    <div id="loading-indicator" class="text-center text-slate-500 dark:text-slate-400 py-10">
        Memuat checklist...
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // --- Variabel Global ---
    // Kita ambil ID Izin GWP dari variabel $permit yang dikirim Controller
    const PERMIT_GWP_ID = {{ $permit->id }};
    const toastContainer = document.getElementById('toast-container');
    const checklistContainer = document.getElementById('checklist-container');
    const loadingIndicator = document.getElementById('loading-indicator');

    // --- Inisialisasi ---
    loadChecklist();

    /**
     * Memuat data checklist (JSON) dari controller
     * dan merendernya ke halaman.
     */
    async function loadChecklist() {
        try {
            // Panggil route DATA (JSON), bukan route VIEW
            const res = await fetch(`{{ route('gwp-cek.index', ['permit_gwp_id' => $permit->id]) }}`, { 
                headers: { 'Accept': 'application/json' } 
            });
            
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Gagal mengambil data checklist');

            // Sembunyikan loading
            loadingIndicator.classList.add('hidden');
            
            // Render 3 grup checklist (jika ada)
            // Nama model ini harus SAMA PERSIS dengan yang ada di controller
            
            // Grup 1: Checklist Pemohon
            renderChecklistGroup(
                json.data['App\\Models\\GwpCekPemohonLs'], 
                'checklist-pemohon', 
                'Checklist Pemohon'
            );
            
            // Grup 2: Checklist HSE
            renderChecklistGroup(
                json.data['App\\Models\\GwpCekHseLs'], 
                'checklist-hse', 
                'Checklist Verifikasi HSE'
            );

            // Grup 3: Checklist Alat
            renderChecklistGroup(
                json.data['App\\Models\\GwpAlatLs'], 
                'checklist-alat', 
                'Checklist Alat yang Digunakan'
            );

        } catch (e) {
            loadingIndicator.innerHTML = `<span class="text-red-500">Gagal memuat data: ${e.message}</span>`;
            showToast('Gagal memuat data checklist!', 'error');
        }
    }

    /**
     * Helper function untuk merender satu grup checklist (misal: "Checklist Pemohon")
     * ke dalam sebuah card.
     */
    function renderChecklistGroup(items, containerId, title) {
        if (!items || items.length === 0) return; // Lewati jika grup ini kosong

        const container = document.getElementById(containerId);
        
        // Buat daftar (list)
        let itemsHtml = items.map(item => {
            // item.id = ID dari gwp_cek (lembar jawaban)
            // item.ls.nama = Nama pertanyaan (dari tabel master)
            // item.value = true/false (sudah dicentang atau belum)
            
            const isChecked = item.value ? 'checked' : '';
            
            return `
                <label class="flex items-center justify-between p-4 border-b dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition duration-150">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">${item.ls.nama}</span>
                    
                    {{-- Checkbox kustom (toggle) --}}
                    <input type="checkbox" 
                           class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500"
                           data-cek-id="${item.id}" 
                           onchange="updateChecklistItem(this)" 
                           ${isChecked}>
                </label>
            `;
        }).join('');

        // Buat card
        container.innerHTML = `
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 p-4 border-b dark:border-slate-700">
                    ${title}
                </h3>
                <div class="divide-y dark:divide-slate-700">
                    ${itemsHtml}
                </div>
            </div>
        `;
    }

    /**
     * [FUNGSI UTAMA] Dipanggil 'onchange' setiap kali checkbox dicentang.
     * Mengirim 'value' (true/false) ke controller via PUT.
     */
    window.updateChecklistItem = async (checkbox) => {
        const cekId = checkbox.dataset.cekId; // ID dari 'gwp_cek'
        const isChecked = checkbox.checked; // true atau false

        // Tampilkan status 'menyimpan'
        checkbox.disabled = true; // Nonaktifkan sementara
        
        try {
            const res = await fetch(`{{ url('gwp-cek') }}/${cekId}`, { 
                method: 'PUT', 
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    value: isChecked // Kirim status centang
                })
            });
            
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Gagal menyimpan.');

            showToast('Checklist diperbarui!', 'success'); 
            
        } catch (e) { 
            showToast(e.message, 'error');
            // Kembalikan ke state semula jika gagal
            checkbox.checked = !isChecked;
        } finally {
            checkbox.disabled = false; // Aktifkan kembali
        }
    }


    /**
     * FUNGSI NOTIFIKASI MODERN (TOAST)
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
        }, 50);

        // Hapus setelah 3 detik
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-10');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>
@endsection