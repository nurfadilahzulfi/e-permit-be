<?php
namespace App\Http\Controllers;

// --- MODEL & LIBRARY YANG DIPERLUKAN ---\
use App\Models\PermitGwp;
use App\Models\PermitGwpApproval;
use Carbon\Carbon;
use Illuminate\Contracts\View\View; // <--- [BARU] DITAMBAHKAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermitGwpApprovalController extends Controller
{
    /**
     * [BARU] Menampilkan halaman frontend (UI) untuk Persetujuan Izin.
     * Ini dipanggil oleh rute '/dashboard/permit-gwp-approval'
     */
    public function view(): View
    {
        // Pastikan Anda memiliki file Blade di:
        // resources/views/permit-gwp-approval/index.blade.php
        return view('permit-gwp-approval.index');
    }

    /**
     * [TETAP] Menampilkan daftar izin yang PERLU PERSETUJUAN SAYA (Supervisor atau HSE)
     * Ini dipanggil oleh rute 'GET /permit-gwp-approval' (dari JavaScript/AJAX)
     */
    public function index()
    {
        $userId = Auth::id(); // Dapatkan ID user yang sedang login
        $data   = PermitGwpApproval::where('approver_id', $userId)
            ->where('status_persetujuan', 0) // 0 = Pending
            ->with(['permitGwp' => function ($query) {
                // Ambil data permit, dan juga data 'pemohon' yang terkait dengan permit itu
                $query->with('pemohon');
            }])
            ->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Menampilkan histori persetujuan untuk satu Izin GWP
     */
    public function show($permit_gwp_id)
    {
        $data = PermitGwpApproval::where('permit_gwp_id', $permit_gwp_id)
            ->with('approver')             // Tampilkan info approver
            ->orderBy('created_at', 'asc') // Urutkan dari yang paling awal
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Histori persetujuan tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    // ===========================================
    // FUNGSI UTAMA: APPROVE DAN REJECT
    // ===========================================

    /**
     * Aksi untuk MENYETUJUI (Approve) izin
     */
    public function approve(Request $request)
    {
        $request->validate(['approval_id' => 'required|integer|exists:permit_gwp_approval,id']);
        $approvalId = $request->input('approval_id');
        $userId     = Auth::id();

        DB::beginTransaction();
        try {
            $approval = PermitGwpApproval::findOrFail($approvalId);
            $permit   = PermitGwp::findOrFail($approval->permit_gwp_id);

            // 1. Pastikan user ini berhak approve
            if ($approval->approver_id !== $userId) {
                return response()->json(['success' => false, 'error' => 'Anda tidak berhak menyetujui izin ini.'], 403);
            }

            // 2. Pastikan izin masih pending (status 0)
            if ($approval->status_persetujuan !== 0) {
                return response()->json(['success' => false, 'error' => 'Izin ini sudah diproses (disetujui/ditolak).'], 422);
            }

                                               // 3. Update status persetujuan
            $approval->status_persetujuan = 1; // 1 = Approved
            $approval->catatan            = $request->input('catatan');
            $approval->tgl_persetujuan    = Carbon::now();
            $approval->save();

            // 4. Cek: Apakah ini persetujuan terakhir?
            $nextApproval = PermitGwpApproval::where('permit_gwp_id', $permit->id)
                ->where('status_persetujuan', 0) // Cari yang masih pending
                ->orderBy('urutan', 'asc')
                ->first();

            if ($nextApproval) {
                                                         // MASIH ADA: Update status permit ke approver selanjutnya
                $permit->status = $nextApproval->urutan; // Misal: urutan 2 (HSE)
            } else {
                                     // SELESAI: Ini adalah persetujuan terakhir
                $permit->status = 3; // 3 = Approved (Final)
            }

            $permit->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Izin telah disetujui.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Aksi untuk MENOLAK (Reject) izin
     */
    public function reject(Request $request)
    {
        $request->validate([
            'approval_id' => 'required|integer|exists:permit_gwp_approval,id',
            'catatan'     => 'required|string|min:5', // Catatan wajib diisi saat reject
        ]);
        $approvalId = $request->input('approval_id');
        $userId     = Auth::id();

        DB::beginTransaction();
        try {
            $approval = PermitGwpApproval::findOrFail($approvalId);
            $permit   = PermitGwp::findOrFail($approval->permit_gwp_id);

            // 1. Pastikan user ini berhak
            if ($approval->approver_id !== $userId) {
                return response()->json(['success' => false, 'error' => 'Anda tidak berhak menolak izin ini.'], 403);
            }

            // 2. Pastikan izin masih pending
            if ($approval->status_persetujuan !== 0) {
                return response()->json(['success' => false, 'error' => 'Izin ini sudah diproses.'], 422);
            }

                                               // 3. Update status persetujuan
            $approval->status_persetujuan = 2; // 2 = Rejected
            $approval->catatan            = $request->input('catatan');
            $approval->tgl_persetujuan    = Carbon::now();
            $approval->save();

                                 // 4. Update status permit utama
            $permit->status = 4; // 4 = Rejected (Final)
            $permit->save();

            // Catatan: Saat satu approver me-reject, alur berhenti.
            // Tidak perlu menghapus log persetujuan lain yang pending,
            // agar histori tetap tercatat.

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Izin telah ditolak.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fungsi 'store' dan 'update' tidak boleh digunakan secara manual.
     * Hapus atau biarkan untuk mengembalikan error.
     */
    public function store(Request $request)
    {
        return response()->json(['success' => false, 'error' => 'Metode ini tidak diizinkan. Gunakan alur persetujuan.'], 405);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['success' => false, 'error' => 'Metode ini tidak diizinkan. Gunakan alur persetujuan.'], 405);
    }

    /**
     * Hapus log approval (Mungkin diperlukan oleh Admin)
     */
    public function destroy($id)
    {
        // $id di sini adalah ID dari 'permit_gwp_approval', BUKAN 'permit_gwp_id'
        DB::beginTransaction();
        try {
            $data = PermitGwpApproval::findOrFail($id);
            $data->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Log persetujuan berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
