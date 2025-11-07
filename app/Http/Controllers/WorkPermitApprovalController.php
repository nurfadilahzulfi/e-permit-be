<?php
namespace App\Http\Controllers;

use App\Models\WorkPermit;
use App\Models\WorkPermitApproval;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkPermitApprovalController extends Controller
{
    /**
     * [BARU] Menampilkan halaman (View) untuk list persetujuan
     */
    public function view(): View
    {
        // Rute ini memanggil file Blade baru di langkah 2
        return view('work-permit-approval.index');
    }

    /**
     * [BARU] Mengambil data (JSON) untuk list persetujuan.
     * Menggantikan fungsi index() dari PermitGwpApprovalController
     */
    public function index()
    {
        $userId = Auth::id(); // Dapatkan ID user yang sedang login

        // Cari persetujuan yang ditugaskan ke user ini dan masih pending
        $data = WorkPermitApproval::where('approver_id', $userId)
            ->where('status_persetujuan', 0) // 0 = Pending
            ->whereHas('workPermit', function ($query) {
                // Pastikan izin induknya belum ditolak atau selesai
                // Status 1 = Pending Checklist, 2 = Pending Approval
                $query->whereIn('status', [1, 2]);
            })
            ->with(['workPermit.pemohon', 'workPermit.hse']) // Ambil relasi dari Induk
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * [BARU] Aksi untuk MENYETUJUI (Approve) izin
     */
    public function approve(Request $request)
    {
        $request->validate([
            'approval_id' => 'required|integer|exists:work_permit_approvals,id',
            'catatan'     => 'nullable|string|max:500',
        ]);

        $approvalId = $request->input('approval_id');
        $userId     = Auth::id();

        DB::beginTransaction();
        try {
            $approval   = WorkPermitApproval::findOrFail($approvalId);
            $workPermit = WorkPermit::findOrFail($approval->work_permit_id);

            // 1. Pastikan user ini berhak approve
            if ($approval->approver_id !== $userId) {
                return response()->json(['success' => false, 'error' => 'Anda tidak berhak menyetujui izin ini.'], 403);
            }

            // 2. Pastikan izin masih pending (status 0)
            if ($approval->status_persetujuan !== 0) {
                return response()->json(['success' => false, 'error' => 'Izin ini sudah diproses.'], 422);
            }

                                               // 3. Update status persetujuan INI
            $approval->status_persetujuan = 1; // 1 = Approved
            $approval->catatan            = $request->input('catatan', 'Disetujui');
            $approval->tgl_persetujuan    = Carbon::now();
            $approval->save();

            // 4. Cek: Apakah ada persetujuan LAIN di urutan yang SAMA?
            // (Contoh: Ada 3 HSE, baru 1 yang approve)
            $otherPending = WorkPermitApproval::where('work_permit_id', $workPermit->id)
                ->where('urutan', $approval->urutan)
                ->where('status_persetujuan', 0) // Masih pending
                ->exists();

            if ($otherPending) {
                // Jika masih ada (misal: HSE lain belum approve), biarkan status induk
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Persetujuan Anda telah dicatat.']);
            }

            // 5. Jika TIDAK ADA, cari persetujuan BERIKUTNYA
            $nextApproval = WorkPermitApproval::where('work_permit_id', $workPermit->id)
                ->where('status_persetujuan', 0) // Cari yang masih pending
                ->orderBy('urutan', 'asc')
                ->first();

            if ($nextApproval) {
                                         // MASIH ADA: Update status permit ke approver selanjutnya
                                         // Misal: dari urutan 1 (HSE) ke urutan 2 (Supervisor)
                $workPermit->status = 2; // (Kita bisa gunakan 'status' untuk mencerminkan urutan)
            } else {
                                         // SELESAI: Ini adalah persetujuan terakhir
                $workPermit->status = 3; // 3 = Approved (Final)
            }

            $workPermit->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Izin telah disetujui.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * [BARU] Aksi untuk MENOLAK (Reject) izin
     */
    public function reject(Request $request)
    {
        $request->validate([
            'approval_id' => 'required|integer|exists:work_permit_approvals,id',
            'catatan'     => 'required|string|min:5', // Catatan wajib diisi saat reject
        ]);

        $approvalId = $request->input('approval_id');
        $userId     = Auth::id();

        DB::beginTransaction();
        try {
            $approval   = WorkPermitApproval::findOrFail($approvalId);
            $workPermit = WorkPermit::findOrFail($approval->work_permit_id);

            // 1. Pastikan user ini berhak
            if ($approval->approver_id !== $userId) {
                return response()->json(['success' => false, 'error' => 'Anda tidak berhak menolak izin ini.'], 403);
            }

                                               // 2. Update status persetujuan INI
            $approval->status_persetujuan = 2; // 2 = Rejected
            $approval->catatan            = $request->input('catatan');
            $approval->tgl_persetujuan    = Carbon::now();
            $approval->save();

                                     // 3. Update status permit utama (Induk)
            $workPermit->status = 4; // 4 = Rejected (Final)
            $workPermit->save();

            // 4. (Opsional) Batalkan semua approval lain yang pending
            WorkPermitApproval::where('work_permit_id', $workPermit->id)
                ->where('status_persetujuan', 0)
                ->update(['status_persetujuan' => 3]); // 3 = Cancelled

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Izin telah ditolak.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
