@extends('layouts.app')

{{-- DIUBAH: Menambahkan section title & page-title --}}
@section('title', 'Manajemen User - E-Permit')
@section('page-title', 'Manajemen User')

@section('content')

<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm">
    </div>

{{-- DIHAPUS: Wrapper <div class="p-4 sm:p-6 lg:p-8"> dihapus karena sudah ada di layout utama --}}

{{-- DIUBAH: gray -> slate --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">Manajemen User</h2>
    <button id="addUserBtn"
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl shadow-lg transition duration-300 transform hover:scale-[1.02]">
        + Tambah User Baru
    </button>
</div>

{{-- DIUBAH: gray -> slate --}}
<div class="sm:hidden text-sm text-slate-500 dark:text-slate-400 mb-2">
    <span class="font-bold">→</span> Geser tabel ke samping untuk melihat semua kolom.
</div>

{{-- DIUBAH: gray -> slate --}}
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Nama</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">NIP</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Divisi</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Email</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Jabatan</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Perusahaan</th>
                    <th class="py-3 px-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-20">Aksi</th>
                </tr>
            </thead>
            <tbody id="userTable" class="text-slate-800 dark:text-slate-200 divide-y divide-slate-200 dark:divide-slate-700">
                </tbody>
        </table>
    </div>
</div>
{{-- DIHAPUS: Penutup </div> dari wrapper padding --}}


<div id="userModal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity duration-300 ease-out opacity-0
            px-4 py-6">
    
    <div id="modalBox"
         class="bg-white dark:bg-slate-900 rounded-2xl w-full sm:w-2/3 md:w-2/3 lg:w-1/2 shadow-2xl transform scale-95 transition-all duration-300 max-h-[90vh]
                h-auto flex flex-col border border-slate-200 dark:border-slate-700">
        
        <div class="flex-shrink-0 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-900 z-10 rounded-t-2xl">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 dark:text-slate-100">Tambah User</h3>
            <button onclick="closeModal()" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 text-2xl transition">&times;</button>
        </div>

        {{-- 
          DIUBAH: 
          1. Parent div dikembalikan ke overflow-y-auto (hanya scroll vertikal).
          2. Kita tambahkan div wrapper BARU di DALAM form dengan 'overflow-x-auto'.
             Ini akan mengisolasi scrollbar horizontal HANYA ke area input.
        --}}
        <div class="px-6 py-4 overflow-y-auto flex-1">
            <form id="userForm">
                @csrf
                <input type="hidden" id="userId">
                
                {{-- 👇 WRAPPER BARU DITAMBAHKAN DI SINI --}}
                <div class="overflow-x-auto">
                    {{-- 
                      Div ini menjamin lebar minimum form, 
                      sehingga di layar sempit pun tidak hancur dan bisa di-scroll.
                    --}}
                    <div style="min-width: 320px;"> 
                
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Nama</label>
                                <input id="nama" name="nama" type="text" required
                                       class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">NIP</label>
                                <input id="nip" name="nip" type="text" required
                                       class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Divisi</label>
                                <input id="divisi" name="divisi" type="text" required
                                       class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Jabatan</label>
                                <input id="jabatan" name="jabatan" type="text" required
                                       class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Perusahaan</label>
                                <input id="perusahaan" name="perusahaan" type="text" required
                                       class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Email</label>
                                <input id="email" name="email" type="email" required
                                       class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
        
                        <div class="mt-4">
                            <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Password</label>
                            <input id="password" name="password" type="password"
                                   class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                            <small id="passwordHelp" class="text-slate-500 dark:text-slate-400 text-xs mt-1 block">(Kosongkan jika tidak ingin mengganti)</small>
                        </div>
                        
                    </div> {{-- Penutup min-width div --}}
                </div> {{-- Penutup overflow-x-auto div --}}
                
            </form>
        </div>

        <div class="flex-shrink-0 px-6 py-3 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-3 bg-white dark:bg-slate-900 z-10 rounded-b-2xl">
            <button onclick="closeModal()" 
                    class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-lg text-slate-700 
                           dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 
                           font-semibold text-sm transition">
                Batal
            </button>
            <button id="saveBtn" type="submit" form="userForm" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white font-semibold text-sm shadow transition">
                Simpan
            </button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Referensi Elemen ---
    const modal = document.getElementById('userModal');
    const modalBox = document.getElementById('modalBox');
    const addUserBtn = document.getElementById('addUserBtn');
    const form = document.getElementById('userForm');
    const saveBtn = document.getElementById('saveBtn');
    const passwordInput = document.getElementById('password');
    const passwordHelp = document.getElementById('passwordHelp');
    const toastContainer = document.getElementById('toast-container');

    // --- Event Listeners ---
    addUserBtn.addEventListener('click', openAddModal);
    form.addEventListener('submit', saveUser);
    
    // --- Inisialisasi ---
    loadUsers();

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
        document.getElementById('userId').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah User Baru';
        passwordInput.required = true;
        passwordHelp.textContent = 'Password wajib diisi.';
        showModal();
    }

    // --- CRUD ---
    async function loadUsers() {
        const tbody = document.getElementById('userTable');
        {{-- DIUBAH: gray -> slate --}}
        tbody.innerHTML = `<tr><td colspan="7" class="py-4 px-4 text-center text-slate-400">Memuat data...</td></tr>`;
        try {
            const res = await fetch('{{ route("user.index") }}', { headers: { 'Accept': 'application/json' } });
            const json = await res.json();

            if (!res.ok) throw new Error(json.message || 'Gagal mengambil data');
            
            tbody.innerHTML = ''; // Bersihkan loading
            
            if (json.data.length === 0) {
                 {{-- DIUBAH: gray -> slate --}}
                tbody.innerHTML = `<tr><td colspan="7" class="py-4 px-4 text-center text-slate-400">Belum ada data user.</td></tr>`;
                return;
            }

            json.data.forEach(u => {
                {{-- 
                  DIUBAH: 
                  1. hover:bg-gray-50 -> hover:bg-slate-50
                  2. dark:hover:bg-gray-800 -> dark:hover:bg-slate-700 (Memperbaiki bug hover)
                  3. Warna tombol Edit/Hapus disesuaikan untuk dark mode
                --}}
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${u.nama}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${u.nip}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${u.divisi}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${u.email}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${u.jabatan}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${u.perusahaan}</td>
                        <td class="py-3 px-4 text-center text-sm whitespace-nowrap">
                            <button onclick="editUser(${u.id})" 
                                    class="text-yellow-600 dark:text-yellow-500 hover:text-yellow-700 dark:hover:text-yellow-400 font-semibold mr-3 transition">
                                Edit
                            </button>
                            <button onclick="deleteUser(${u.id})" 
                                    class="text-red-600 dark:text-red-500 hover:text-red-700 dark:hover:text-red-400 font-semibold transition">
                                Hapus
                            </button>
                        </td>
                    </tr>`;
            });
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="7" class="py-4 px-4 text-center text-red-500">Gagal memuat data: ${e.message}</td></tr>`;
            showToast('Gagal memuat data!', 'error');
        }
    }

    window.editUser = async (id) => {
        try {
            const res = await fetch(`/user/${id}`, { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Gagal mengambil data user.');

            const u = json.data;
            ['nama','nip','divisi','jabatan','perusahaan','email'].forEach(k => document.getElementById(k).value = u[k]);
            document.getElementById('userId').value = u.id;
            document.getElementById('modalTitle').innerText = 'Edit User: ' + u.nama;
            passwordInput.required = false;
            passwordInput.value = ''; // Selalu kosongkan password saat edit
            passwordHelp.textContent = '(Kosongkan jika tidak ingin mengganti)';
            showModal();
        } catch (e) {
            showToast(e.message, 'error');
        }
    }

    async function saveUser(e) {
        e.preventDefault();
        const id = document.getElementById('userId').value;
        const formData = new FormData(form);
        
        if (id && !formData.get('password')) formData.delete('password');
        
        let url = id ? `/user/${id}` : '{{ route("user.store") }}';
        if (id) formData.append('_method', 'PUT');
        
        saveBtn.disabled = true; 
        saveBtn.innerHTML = '<span class="animate-spin">⏳</span> Menyimpan...'; // Loading state

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            });
            
            const data = await res.json();

            if (!res.ok) {
                // --- PENANGANAN ERROR VALIDASI (422) ---
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
                loadUsers();
            }

        } catch (e) {
            showToast(e.message, 'error');
        } finally {
            saveBtn.disabled = false; 
            saveBtn.textContent = 'Simpan';
        }
    }

    window.deleteUser = async (id) => {
        // Ganti ini dengan modal konfirmasi yang lebih baik jika Anda mau
        if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) return;
        
        try {
            const res = await fetch(`/user/${id}`, { 
                method: 'DELETE', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } 
            });
            const data = await res.json();
            if (data.success) { 
                showToast('User berhasil dihapus!', 'success'); 
                loadUsers(); 
            } else {
                throw new Error(data.message || 'Gagal menghapus user.');
            }
        } catch (e) { 
            showToast(e.message, 'error'); 
        }
    }

    /**
     * FUNGSI NOTIFIKASI MODERN (Bisa Menumpuk)
     * @param {string} message - Pesan yang ingin ditampilkan.
     * @param {string} type - 'success' (hijau) atau 'error' (merah).
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
        
        // Tambahkan ke kontainer
        toastContainer.appendChild(toast);
        
        // Animasi masuk
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-x-10');
            toast.classList.add('opacity-100', 'translate-x-0');
        }, 50);

        // Hapus setelah 3.5 detik
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-10');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }
});
</script>
@endsection