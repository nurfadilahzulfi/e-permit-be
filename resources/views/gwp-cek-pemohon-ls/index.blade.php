@extends('layouts.app')

@section('title', 'Manajemen Checklist Pemohon - E-Permit')
@section('page-title', 'Manajemen Checklist Pemohon')

@section('content')

<!-- KONTENER TOAST (Untuk Notifikasi) -->
<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm">
    <!-- Notifikasi akan muncul di sini -->
</div>

<!-- HEADER HALAMAN -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">
        Master Checklist Pemohon
    </h2>
    <button id="addBtn"
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl shadow-lg transition duration-300 transform hover:scale-[1.02]">
        + Tambah Pertanyaan Baru
    </button>
</div>

<!-- TABEL DAFTAR -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Nama Pertanyaan Checklist</th>
                    <th class="py-3 px-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-20">Aksi</th>
                </tr>
            </thead>
            <tbody id="itemTable" class="text-slate-800 dark:text-slate-200 divide-y divide-slate-200 dark:divide-slate-700">
                <!-- Data dimuat oleh JS -->
            </tbody>
        </table>
    </div>
</div>


{{-- MODAL UNTUK TAMBAH / EDIT --}}
<div id="itemModal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity duration-300 ease-out opacity-0
            px-4 py-6">
    
    <div id="modalBox"
         class="bg-white dark:bg-slate-900 rounded-2xl w-full sm:w-2/3 md:w-1/2 lg:w-1/3 shadow-2xl transform scale-95 transition-all duration-300 max-h-[90vh]
                h-auto flex flex-col border border-slate-200 dark:border-slate-700">
        
        <!-- HEADER -->
        <div class="flex-shrink-0 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-900 z-10 rounded-t-2xl">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 dark:text-slate-100">Tambah Pertanyaan</h3>
            <button onclick="closeModal()" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 text-2xl transition">&times;</button>
        </div>

        <!-- BODY -->
        <div class="px-6 py-4 overflow-y-auto flex-1">
            <form id="itemForm">
                @csrf
                <input type="hidden" id="lsId">
                
                <div>
                    <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Nama Pertanyaan</label>
                    <input id="nama" name="nama" type="text" required
                           class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Apakah JSA sudah dibuat?">
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
            <button id="saveBtn" type="submit" form="itemForm"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white font-semibold text-sm shadow transition">
                Simpan
            </button>
        </div>
    </div>
</div>

<script>
// =================================================================
// SCRIPT INI DI-SET UNTUK 'gwp-cek-pemohon-ls'
// =================================================================
document.addEventListener('DOMContentLoaded', () => {
    
    // --- Variabel & Referensi ---
    const R_INDEX = '{{ route("gwp-cek-pemohon-ls.index") }}';
    const R_STORE = '{{ route("gwp-cek-pemohon-ls.store") }}';
    const R_PREFIX = 'gwp-cek-pemohon-ls'; // prefix for update/delete/show

    const modal = document.getElementById('itemModal');
    const modalBox = document.getElementById('modalBox');
    const addBtn = document.getElementById('addBtn');
    const form = document.getElementById('itemForm');
    const saveBtn = document.getElementById('saveBtn');
    const toastContainer = document.getElementById('toast-container');

    // --- Event Listeners ---
    addBtn.addEventListener('click', openAddModal);
    form.addEventListener('submit', saveItem);
    
    // --- Inisialisasi ---
    loadItems();

    // --- Modal ---
    function showModal() {
        modal.classList.remove('hidden', 'opacity-0');
        modal.classList.add('flex', 'opacity-100');
        modalBox.classList.replace('scale-95', 'scale-100');
    }
    window.closeModal = function() {
        modal.classList.replace('opacity-100', 'opacity-0');
        modalBox.classList.replace('scale-100', 'scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
    function openAddModal() {
        form.reset();
        document.getElementById('lsId').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Pertanyaan Baru';
        showModal();
    }

    // --- CRUD ---
    async function loadItems() {
        const tbody = document.getElementById('itemTable');
        tbody.innerHTML = `<tr><td colspan="2" class="py-4 px-4 text-center text-slate-400">Memuat data...</td></tr>`;
        try {
            const res = await fetch(R_INDEX, { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Gagal mengambil data');
            
            tbody.innerHTML = ''; // Bersihkan loading
            if (json.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="2" class="py-4 px-4 text-center text-slate-400">Belum ada data checklist.</td></tr>`;
                return;
            }
            json.data.forEach(item => {
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${item.nama}</td>
                        <td class="py-3 px-4 text-center text-sm whitespace-nowrap">
                            <button onclick="editItem(${item.id})" 
                                    class="text-yellow-600 dark:text-yellow-500 hover:text-yellow-700 dark:hover:text-yellow-400 font-semibold mr-3 transition">
                                Edit
                            </button>
                            <button onclick="deleteItem(${item.id})" 
                                    class="text-red-600 dark:text-red-500 hover:text-red-700 dark:hover:text-red-400 font-semibold transition">
                                Hapus
                            </button>
                        </td>
                    </tr>`;
            });
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="2" class="py-4 px-4 text-center text-red-500">Gagal memuat data: ${e.message}</td></tr>`;
            showToast('Gagal memuat data!', 'error');
        }
    }

    window.editItem = async (id) => {
        try {
            const res = await fetch(`/${R_PREFIX}/${id}`, { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Gagal mengambil data.');
            const item = json.data;
            document.getElementById('lsId').value = item.id;
            document.getElementById('nama').value = item.nama;
            document.getElementById('modalTitle').innerText = 'Edit Pertanyaan';
            showModal();
        } catch (e) {
            showToast(e.message, 'error');
        }
    }

    async function saveItem(e) {
        e.preventDefault();
        const id = document.getElementById('lsId').value;
        const formData = new FormData(form);
        
        let url = id ? `/${R_PREFIX}/${id}` : R_STORE;
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
                loadItems(); 
            }
        } catch (e) {
            showToast(e.message, 'error');
        } finally {
            saveBtn.disabled = false; 
            saveBtn.textContent = 'Simpan';
        }
    }

    window.deleteItem = async (id) => {
        if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;
        try {
            const res = await fetch(`/${R_PREFIX}/${id}`, { 
                method: 'DELETE', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } 
            });
            const data = await res.json();
            if (data.success) { 
                showToast('Data berhasil dihapus!', 'success'); 
                loadItems(); 
            } else {
                throw new Error(data.message || 'Gagal menghapus data.');
            }
        } catch (e) { 
            showToast(e.message, 'error'); 
        }
    }

    // --- Notifikasi ---
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
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-x-10');
            toast.classList.add('opacity-100', 'translate-x-0');
        }, 50);
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-10');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>
@endsection