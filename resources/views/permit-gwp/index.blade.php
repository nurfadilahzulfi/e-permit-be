@extends('layouts.app')

{{-- DIUBAH: Title untuk halaman Permit GWP --}}
@section('title', 'Manajemen Permit GWP - E-Permit')
@section('page-title', 'Manajemen Permit GWP')

@section('content')

<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm">
    </div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">
        Manajemen Permit GWP
    </h2>
    {{-- DIUBAH: ID Tombol diganti --}}
    <button id="addGwpBtn"
            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl shadow-lg transition duration-300 transform hover:scale-[1.02]">
        + Ajukan Izin GWP Baru
    </button>
</div>

<div class="sm:hidden text-sm text-slate-500 dark:text-slate-400 mb-2">
    <span class="font-bold">→</span> Geser tabel ke samping untuk melihat semua kolom.
</div>

<div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    {{-- DIUBAH: Kolom tabel diganti --}}
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Nomor Izin</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Lokasi</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Tgl. Diajukan</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Status</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Berlaku Hingga</th>
                    <th class="py-3 px-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-20">Aksi</th>
                </tr>
            </thead>
            {{-- DIUBAH: ID tabel diganti --}}
            <tbody id="gwpTable" class="text-slate-800 dark:text-slate-200 divide-y divide-slate-200 dark:divide-slate-700">
                </tbody>
        </table>
    </div>
</div>


{{-- 
=====================================
MODAL UNTUK TAMBAH / EDIT IZIN GWP
=====================================
--}}
{{-- DIUBAH: ID Modal diganti --}}
<div id="gwpModal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity duration-300 ease-out opacity-0
            px-4 py-6">
    
    <div id="modalBox"
         class="bg-white dark:bg-slate-900 rounded-2xl w-full sm:w-2/3 md:w-2/3 lg:w-1/2 shadow-2xl transform scale-95 transition-all duration-300 max-h-[90vh]
                h-auto flex flex-col border border-slate-200 dark:border-slate-700">
        
        <div class="flex-shrink-0 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-white dark:bg-slate-900 z-10 rounded-t-2xl">
            {{-- DIUBAH: Title Modal diganti --}}
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800 dark:text-slate-100">Ajukan Izin GWP Baru</h3>
            <button onclick="closeModal()" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 text-2xl transition">&times;</button>
        </div>

        <div class="px-6 py-4 overflow-y-auto flex-1">
            {{-- DIUBAH: ID Form diganti --}}
            <form id="gwpForm">
                @csrf
                <input type="hidden" id="gwpId">
                
                <div class="overflow-x-auto">
                    <div style="min-width: 320px;"> 
                
                        {{-- DIUBAH: Form diganti total --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Nomor Izin</label>
                                <input id="nomor" name="nomor" type="text" required
                                       class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Jenis Izin</label>
                                <select id="permit_type_id" name="permit_type_id" required
                                        class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                                    {{-- Opsi dimuat oleh JS --}}
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Shift Kerja</label>
                                <input id="shift_kerja" name="shift_kerja" type="text" required
                                       class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Shift 1 (08:00 - 17:00)">
                            </div>
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Lokasi Pekerjaan</label>
                                <input id="lokasi" name="lokasi" type="text" required
                                       class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
        
                        <div class="mt-4">
                            <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Deskripsi Pekerjaan</label>
                            <textarea id="deskripsi_pekerjaan" name="deskripsi_pekerjaan" rows="3" required
                                   class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <div class="mt-4">
                            <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Peralatan Pekerjaan</label>
                            <textarea id="peralatan_pekerjaan" name="peralatan_pekerjaan" rows="2" required
                                   class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">Jenis Pemohon</label>
                                <select id="pemohon_jenis" name="pemohon_jenis" required
                                        class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                                    <option value="internal">Internal</option>
                                    <option value="eksternal">Eksternal</option>
                                </select>
                            </div>
                            
                            {{-- INI ADALAH INPUT KUNCI (POIN 1) --}}
                            <div>
                                <label class="text-slate-700 dark:text-slate-200 text-sm font-medium">
                                    Supervisor (Pemilik Lokasi)
                                </label>
                                <select id="supervisor_id" name="supervisor_id" required
                                        class="w-full mt-1 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500">
                                    {{-- Opsi dimuat oleh JS dari data user --}}
                                </select>
                            </div>
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
            <button id="saveBtn" type="submit" form="gwpForm" {{-- DIUBAH: form="gwpForm" --}}
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white font-semibold text-sm shadow transition">
                Simpan & Ajukan
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Referensi Elemen ---
    const modal = document.getElementById('gwpModal');
    const modalBox = document.getElementById('modalBox');
    const addBtn = document.getElementById('addGwpBtn');
    const form = document.getElementById('gwpForm');
    const saveBtn = document.getElementById('saveBtn');
    const toastContainer = document.getElementById('toast-container');
    const supervisorSelect = document.getElementById('supervisor_id');
    const permitTypeSelect = document.getElementById('permit_type_id');

    // --- Event Listeners ---
    addBtn.addEventListener('click', openAddModal);
    form.addEventListener('submit', savePermit);
    
    // --- Inisialisasi ---
    loadPermits();
    // Kita muat data supervisor & permit type sekali saja di awal
    loadSupervisors(); 
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
        document.getElementById('gwpId').value = '';
        document.getElementById('modalTitle').innerText = 'Ajukan Izin GWP Baru';
        showModal();
    }

    // --- FUNGSI BARU UNTUK MENGISI DROPDOWN ---

    /**
     * Memuat daftar Supervisor (User) untuk dropdown
     */
    async function loadSupervisors() {
        try {
            const res = await fetch('{{ route("user.index") }}', { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (!json.success) throw new Error('Gagal memuat data user');

            supervisorSelect.innerHTML = '<option value="">Pilih Supervisor...</option>'; // Opsi default
            
            json.data.forEach(user => {
                // Asumsi: Supervisor adalah yang jabatannya 'Supervisor'
                if (user.jabatan.toLowerCase() === 'supervisor') {
                    const option = new Option(user.nama, user.id);
                    supervisorSelect.add(option);
                }
            });
        } catch (e) {
            console.error(e);
            supervisorSelect.innerHTML = '<option value="">Gagal memuat supervisor</option>';
        }
    }

    /**
     * Memuat daftar Jenis Izin (Permit Types) untuk dropdown
     */
    async function loadPermitTypes() {
        try {
            const res = await fetch('{{ route("permit-types.index") }}', { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (!json.success) throw new Error('Gagal memuat data jenis izin');

            permitTypeSelect.innerHTML = '<option value="">Pilih Jenis Izin...</option>'; // Opsi default
            
            json.data.forEach(type => {
                const option = new Option(`${type.kode} - ${type.nama}`, type.id);
                permitTypeSelect.add(option);
            });
        } catch (e) {
            console.error(e);
            permitTypeSelect.innerHTML = '<option value="">Gagal memuat jenis izin</option>';
        }
    }


    // --- CRUD ---

    /**
     * Memuat daftar Izin GWP (Poin 3)
     */
    async function loadPermits() {
        const tbody = document.getElementById('gwpTable');
        tbody.innerHTML = `<tr><td colspan="6" class="py-4 px-4 text-center text-slate-400">Memuat data...</td></tr>`;
        try {
            // DIUBAH: Fetch ke route GWP
            const res = await fetch('{{ route("permit-gwp.index") }}', { headers: { 'Accept': 'application/json' } });
            const json = await res.json();

            if (!res.ok) throw new Error(json.message || 'Gagal mengambil data');
            
            tbody.innerHTML = ''; // Bersihkan loading
            
            if (json.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="py-4 px-4 text-center text-slate-400">Belum ada data izin GWP.</td></tr>`;
                return;
            }

            // DIUBAH: Loop data GWP
            json.data.forEach(p => {
                
                // LOGIC STATUS (POIN 3)
                let statusText = '';
                let statusClass = '';
                switch (p.status) {
                    case 1: statusText = 'Pending SPV'; statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'; break;
                    case 2: statusText = 'Pending HSE'; statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'; break;
                    case 3: statusText = 'Approved'; statusClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'; break;
                    case 4: statusText = 'Rejected'; statusClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'; break;
                    default: statusText = 'Draft'; statusClass = 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200';
                }

                // Format tanggal
                const tglDiajukan = new Date(p.tgl_permohonan).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                const tglBerlaku = p.valid_until ? new Date(p.valid_until).toLocaleString('id-ID', { dateStyle: 'medium' }) : '-';

                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        <td class="py-3 px-4 text-sm whitespace-nowrap font-medium text-blue-600 dark:text-blue-400">${p.nomor}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${p.lokasi}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${tglDiajukan}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${tglBerlaku}</td>
                        <td class="py-3 px-4 text-center text-sm whitespace-nowrap">
                            <button onclick="editPermit(${p.id})" 
                                    class="text-yellow-600 dark:text-yellow-500 hover:text-yellow-700 dark:hover:text-yellow-400 font-semibold mr-3 transition">
                                Edit
                            </button>
                            <button onclick="deletePermit(${p.id})" 
                                    class="text-red-600 dark:text-red-500 hover:text-red-700 dark:hover:text-red-400 font-semibold transition">
                                Hapus
                            </button>
                        </td>
                    </tr>`;
            });
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="6" class="py-4 px-4 text-center text-red-500">Gagal memuat data: ${e.message}</td></tr>`;
            showToast('Gagal memuat data!', 'error');
        }
    }

    /**
     * Membuka modal Edit
     */
    window.editPermit = async (id) => {
        try {
            const res = await fetch(`/permit-gwp/${id}`, { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Gagal mengambil data.');

            const p = json.data;
            // Isi semua field di form
            document.getElementById('gwpId').value = p.id;
            document.getElementById('nomor').value = p.nomor;
            document.getElementById('permit_type_id').value = p.permit_type_id;
            document.getElementById('shift_kerja').value = p.shift_kerja;
            document.getElementById('lokasi').value = p.lokasi;
            document.getElementById('deskripsi_pekerjaan').value = p.deskripsi_pekerjaan;
            document.getElementById('peralatan_pekerjaan').value = p.peralatan_pekerjaan;
            document.getElementById('pemohon_jenis').value = p.pemohon_jenis;
            document.getElementById('supervisor_id').value = p.supervisor_id;
            
            document.getElementById('modalTitle').innerText = 'Edit Izin: ' + p.nomor;
            showModal();
        } catch (e) {
            showToast(e.message, 'error');
        }
    }

    /**
     * Menyimpan data (Create atau Update)
     */
    async function savePermit(e) {
        e.preventDefault();
        const id = document.getElementById('gwpId').value;
        const formData = new FormData(form);
        
        let url = id ? `/permit-gwp/${id}` : '{{ route("permit-gwp.store") }}';
        if (id) formData.append('_method', 'PUT'); // Method spoofing untuk Update
        
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
                loadPermits(); // Muat ulang tabel
            }

        } catch (e) {
            showToast(e.message, 'error');
        } finally {
            saveBtn.disabled = false; 
            saveBtn.textContent = 'Simpan & Ajukan';
        }
    }

    /**
     * Menghapus data
     */
    window.deletePermit = async (id) => {
        if (!confirm('Apakah Anda yakin ingin menghapus izin ini?')) return;
        
        try {
            const res = await fetch(`/permit-gwp/${id}`, { 
                method: 'DELETE', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } 
            });
            const data = await res.json();
            if (data.success) { 
                showToast('Izin berhasil dihapus!', 'success'); 
                loadPermits(); // Muat ulang tabel
            } else {
                throw new Error(data.message || 'Gagal menghapus izin.');
            }
        } catch (e) { 
            showToast(e.message, 'error'); 
        }
    }

    /**
     * FUNGSI NOTIFIKASI MODERN (Bisa Menumpuk)
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

        // Hapus setelah 3.5 detik
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-x-10');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }
});
</script>
@endsection