@extends('layouts.app')

{{-- DIUBAH: Title untuk halaman Permit Types --}}
@section('title', 'Manajemen Jenis Izin - E-Permit')
@section('page-title', 'Manajemen Jenis Izin')

@section('content')

<!-- KONTENER TOAST (Untuk Notifikasi) -->
<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm">
    <!-- Notifikasi akan muncul di sini -->
</div>

<!-- HEADER HALAMAN -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">
        Manajemen Jenis Izin
    </h2>
    <button id="addTypeBtn"
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl shadow-lg transition duration-300 transform hover:scale-[1.02]">
        + Tambah Jenis Izin Baru
    </button>
</div>

<!-- Petunjuk Scroll untuk Mobile -->
<div class="sm:hidden text-sm text-slate-500 dark:text-slate-400 mb-2">
    <span class="font-bold">→</span> Geser tabel ke samping untuk melihat semua kolom.
</div>

<!-- TABEL DAFTAR JENIS IZIN -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    {{-- DIUBAH: Kolom tabel diganti --}}
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Kode</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Nama Jenis Izin</th>
                    <th class="py-3 px-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-20">Aksi</th>
                </tr>
            </thead>
            <tbody id="typeTable" class="text-slate-800 dark:text-slate-200 divide-y divide-slate-200 dark:divide-slate-700">
                <!-- Data dimuat oleh JS -->
            </tbody>
        </table>
    </div>
</div>


{{-- MODAL UNTUK TAMBAH / EDIT JENIS IZIN --}}
<div id="typeModal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity duration-300 ease-out opacity-0
            px-4 py-6">
    
    <div id="modalBox"
         class="bg-white dark:bg-slate-900 rounded-2xl w-full sm:w-2/3 md:w-1/2 lg:w-1/3 shadow-2xl transform scale-95 transition-all duration-300 max-h-[90vh]
                h-auto flex flex-col border border-slate-200 dark:border-slate-700">
        
        <!-- HEADER -->
        <div class="flex-shrink-0 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-900 z-10 rounded-t-2xl">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 dark:text-slate-100">Tambah Jenis Izin</h3>
            <button onclick="closeModal()" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 text-2xl transition">&times;</button>
        </div>

        <!-- BODY -->
        <div class="px-6 py-4 overflow-y-auto flex-1">
            <form id="typeForm">
                @csrf
                <input type="hidden" id="typeId">
                
                {{-- DIUBAH: Form diganti total --}}
                <div>
                    <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Kode Izin</label>
                    <input id="kode" name="kode" type="text" required
                           class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500" placeholder="Contoh: GWP">
                </div>

                <div class="mt-4">
                    <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Nama Izin</label>
                    <input id="nama" name="nama" type="text" required
                           class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500" placeholder="Contoh: General Work Permit">
                </div>
                
            </form>
        </div>

        <!-- FOOTER -->
        <div class="flex-shrink-0 px-6 py-3 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-3 bg-white dark:bg-slate-900 z-10 rounded-b-2xl">
            <button onclick="closeModal()" 
                    class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-lg text-slate-700 
                           dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 
                           font-semibold text-sm transition">
                Batal
            </button>
            <button id="saveBtn" type="submit" form="typeForm"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white font-semibold text-sm shadow transition">
                Simpan
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Referensi Elemen ---
    const modal = document.getElementById('typeModal');
    const modalBox = document.getElementById('modalBox');
    const addBtn = document.getElementById('addTypeBtn');
    const form = document.getElementById('typeForm');
    const saveBtn = document.getElementById('saveBtn');
    const toastContainer = document.getElementById('toast-container');

    // --- Event Listeners ---
    addBtn.addEventListener('click', openAddModal);
    form.addEventListener('submit', saveType);
    
    // --- Inisialisasi ---
    loadPermitTypes();

    // --- Modal ---
    function showModal() {
        modal.classList.remove('hidden', 'opacity-0');
        modal.classList.add('flex', 'opacity-100');
        modalBox.classList.replace('scale-95', 'scale-100');
    }

    function closeModal() {
        modal.classList.replace('opacity-100', 'opacity-0');
        modalBox.classList.replace('scale-100', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
    window.closeModal = closeModal; // Agar bisa dipanggil dari HTML

    function openAddModal() {
        form.reset();
        document.getElementById('typeId').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Jenis Izin Baru';
        showModal();
    }

    // --- CRUD ---
    async function loadPermitTypes() {
        const tbody = document.getElementById('typeTable');
        tbody.innerHTML = `<tr><td colspan="3" class="py-4 px-4 text-center text-slate-400">Memuat data...</td></tr>`;
        try {
            const res = await fetch('{{ route("permit-types.index") }}', { headers: { 'Accept': 'application/json' } });
            const json = await res.json();

            if (!res.ok) throw new Error(json.message || 'Gagal mengambil data');
            
            tbody.innerHTML = ''; // Bersihkan loading
            
            if (json.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" class="py-4 px-4 text-center text-slate-400">Belum ada data Jenis Izin.</td></tr>`;
                return;
            }

            json.data.forEach(type => {
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        <td class="py-3 px-4 text-sm whitespace-nowrap font-medium text-slate-900 dark:text-slate-100">${type.kode}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${type.nama}</td>
                        <td class="py-3 px-4 text-center text-sm whitespace-nowrap">
                            <button onclick="editType(${type.id})" 
                                    class="text-yellow-600 dark:text-yellow-500 hover:text-yellow-700 dark:hover:text-yellow-400 font-semibold mr-3 transition">
                                Edit
                            </button>
                            <button onclick="deleteType(${type.id})" 
                                    class="text-red-600 dark:text-red-500 hover:text-red-700 dark:hover:text-red-400 font-semibold transition">
                                Hapus
                            </button>
                        </td>
                    </tr>`;
            });
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="3" class="py-4 px-4 text-center text-red-500">Gagal memuat data: ${e.message}</td></tr>`;
            showToast('Gagal memuat data!', 'error');
        }
    }

    window.editType = async (id) => {
        try {
            const res = await fetch(`/permit-types/${id}`, { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Gagal mengambil data.');

            const type = json.data;
            document.getElementById('typeId').value = type.id;
            document.getElementById('kode').value = type.kode;
            document.getElementById('nama').value = type.nama;
            
            document.getElementById('modalTitle').innerText = 'Edit Jenis Izin: ' + type.kode;
            showModal();
        } catch (e) {
            showToast(e.message, 'error');
        }
    }

    async function saveType(e) {
        e.preventDefault();
        const id = document.getElementById('typeId').value;
        const formData = new FormData(form);
        
        let url = id ? `/permit-types/${id}` : '{{ route("permit-types.store") }}';
        if (id) formData.append('_method', 'PUT');
        
        saveBtn.disabled = true; 
        saveBtn.innerHTML = '<span class="animate-spin">⏳</span> Menyimpan...';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            });
            
            const data = await res.json();

            if (!res.ok) {
                if (res.status === 422 && data.errors) {
                    const validationErrors = Object.values(data.errors).flat();
                    const errorMessage = validationErrors.join('<br>');
                    showToast(`<strong>Gagal Validasi:</strong><br>${errorMessage}`, 'error');
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } else if (data.success) {
                showToast(data.message || 'Berhasil!', 'success');
                closeModal(); 
                loadPermitTypes(); // Muat ulang tabel
            }

        } catch (e) {
            showToast(e.message, 'error');
        } finally {
            saveBtn.disabled = false; 
            saveBtn.textContent = 'Simpan';
        }
    }

    window.deleteType = async (id) => {
        if (!confirm('Apakah Anda yakin ingin menghapus jenis izin ini?')) return;
        
        try {
            const res = await fetch(`/permit-types/${id}`, { 
                method: 'DELETE', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } 
            });
            const data = await res.json();
            if (data.success) { 
                showToast('Jenis izin berhasil dihapus!', 'success'); 
                loadPermitTypes(); // Muat ulang tabel
            } else {
                throw new Error(data.message || 'Gagal menghapus data.');
            }
        } catch (e) { 
            showToast(e.message, 'error'); 
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