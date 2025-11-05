@extends('layouts.app')

{{-- DIUBAH: Title untuk halaman approval --}}
@section('title', 'Persetujuan Izin - E-Permit')
@section('page-title', 'Persetujuan Izin GWP')

@section('content')

<div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-3 w-full max-w-sm">
    </div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 dark:border-slate-700">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 mb-3 sm:mb-0">
        Menunggu Persetujuan Anda
    </h2>
    {{-- Tombol "Tambah" sudah dihapus --}}
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
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Pemohon</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Lokasi</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Tgl. Diajukan</th>
                    <th class="py-3 px-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Role Anda</th>
                    <th class="py-3 px-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-40">Aksi</th>
                </tr>
            </thead>
            {{-- DIUBAH: ID tabel diganti --}}
            <tbody id="approvalTable" class="text-slate-800 dark:text-slate-200 divide-y divide-slate-200 dark:divide-slate-700">
                <!-- Data dimuat oleh JS -->
            </tbody>
        </table>
    </div>
</div>

{{-- Modal "Tambah/Edit User" sudah dihapus --}}

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Referensi Elemen ---
    const toastContainer = document.getElementById('toast-container');
    
    // --- Inisialisasi ---
    loadTasks(); // Ganti loadUsers() -> loadTasks()

    // --- CRUD ---
    async function loadTasks() {
        const tbody = document.getElementById('approvalTable');
        tbody.innerHTML = `<tr><td colspan="6" class="py-4 px-4 text-center text-slate-400">Memuat data persetujuan...</td></tr>`;
        
        try {
            // DIUBAH: Fetch ke route approval
            const res = await fetch('{{ route("permit-gwp-approval.index") }}', { 
                headers: { 'Accept': 'application/json' } 
            });
            const json = await res.json();

            if (!res.ok) throw new Error(json.message || 'Gagal mengambil data');
            
            tbody.innerHTML = ''; // Bersihkan loading
            
            if (json.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="py-4 px-4 text-center text-slate-400">Tidak ada izin yang menunggu persetujuan Anda.</td></tr>`;
                return;
            }

            // DIUBAH: Loop data approval
            json.data.forEach(task => {
                // 'task' adalah data dari 'PermitGwpApproval'
                // 'permit' adalah data dari 'PermitGwp' (nested)
                // 'pemohon' adalah data dari 'User' (nested di dalam permit)
                const permit = task.permit_gwp;
                const pemohon = permit.pemohon ? permit.pemohon.nama : '(User T/A)';
                const tglDiajukan = new Date(permit.tgl_permohonan).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                
                // 'permit_gwp_id' adalah ID Izin GWP (bukan ID task approval)
                const permit_gwp_id = permit.id; 

                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        <td class="py-3 px-4 text-sm whitespace-nowrap font-medium text-blue-600 dark:text-blue-400">
                            ${permit.nomor}
                        </td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${pemohon}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${permit.lokasi}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">${tglDiajukan}</td>
                        <td class="py-3 px-4 text-sm whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                ${task.role_persetujuan}
                            </span>
                        </td>
                        
                        {{-- 👇👇 INI PERUBAHANNYA 👇👇 --}}
                        <td class="py-3 px-4 text-center text-sm whitespace-nowrap">
                            
                            <!-- TOMBOL BARU: Link ke Halaman Checklist -->
                            <a href="{{ url('gwp-cek/view') }}/${permit_gwp_id}" 
                               class="text-blue-600 dark:text-blue-400 hover:underline font-semibold transition mr-3"
                               target="_blank" title="Lihat/Isi Checklist">
                               Cek Detail
                            </a>

                            <button onclick="rejectTask(${permit_gwp_id})" 
                                    class="text-red-600 dark:text-red-500 hover:text-red-700 dark:hover:text-red-400 font-semibold transition mr-3">
                                Tolak
                            </button>
                            <button onclick="approveTask(${permit_gwp_id})" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-1 px-3 rounded-lg transition">
                                Setujui
                            </button>
                        </td>
                        {{-- 👆👆 BATAS PERUBAHAN 👆👆 --}}

                    </tr>`;
            });
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="6" class="py-4 px-4 text-center text-red-500">Gagal memuat data: ${e.message}</td></tr>`;
            showToast('Gagal memuat data!', 'error');
        }
    }

    /**
     * FUNGSI BARU: Menyetujui Izin
     */
    window.approveTask = async (permitGwpId) => {
        if (!confirm('Anda yakin ingin MENYETUJUI izin ini?')) return;
        
        try {
            const res = await fetch(`/permit-gwp-approval/approve/${permitGwpId}`, { 
                method: 'POST', 
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Accept': 'application/json' 
                } 
            });
            const data = await res.json();
            
            if (!res.ok) throw new Error(data.message || 'Gagal menyetujui.');

            showToast(data.message || 'Izin berhasil disetujui!', 'success'); 
            loadTasks(); // Muat ulang daftar
            
        } catch (e) { 
            showToast(e.message, 'error'); 
        }
    }

    /**
     * FUNGSI BARU: Menolak Izin
     */
    window.rejectTask = async (permitGwpId) => {
        const catatan = prompt('Wajib diisi: Apa alasan Anda MENOLAK izin ini? (min. 10 karakter)');

        if (!catatan) return; // Jika user klik "Cancel"

        if (catatan.length < 10) {
            showToast('Alasan penolakan wajib diisi, minimal 10 karakter.', 'error');
            return;
        }

        try {
            const res = await fetch(`/permit-gwp-approval/reject/${permitGwpId}`, { 
                method: 'POST', 
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Accept': 'application/json',
                    'Content-Type': 'application/json' // Kirim data sebagai JSON
                },
                body: JSON.stringify({ catatan: catatan }) // Kirim catatan di body
            });
            const data = await res.json();
            
            if (!res.ok) throw new Error(data.message || 'Gagal menolak.');

            showToast(data.message || 'Izin berhasil ditolak.', 'success'); 
            loadTasks(); // Muat ulang daftar
            
        } catch (e) { 
            showToast(e.message, 'error'); 
        }
    }


    /**
     * FUNGSI NOTIFIKASI MODERN (Bisa Menumpuk)
     * (Ini kita simpan dari template, sangat berguna)
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