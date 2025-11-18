<?php
namespace App\Http\Controllers;

// ===== MODEL & LIBRARY YANG DIBUTUHKAN =====
use App\Models\WorkPermit;
use App\Models\WorkPermitApproval;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkPermitApprovalController extends Controller
{
    /**
     * Menampilkan halaman frontend (UI) untuk Persetujuan Izin.
     */
    public function view(): View
    {
        return view('work-permit-approval.index');
    }

    /**
     * Mengembalikan data JSON dari izin yang perlu SAYA setujui.
     */
    /**
     * Mengembalikan data JSON dari izin yang perlu SAYA setujui.
     */
    public function index()
    {
        $userId = Auth::id();

                                                         // 1. Ambil semua WorkPermit yang sedang menunggu persetujuan (Status 2)
        $pendingPermits = WorkPermit::where('status', 2) // Status 2 = Pending Approval
            ->with([
                'pemohon',
                'supervisor',
                'hse',
                // Muat HANYA approval yang belum selesai
                'approvals' => function ($query) {
                    $query->where('status_persetujuan', 0)->orderBy('urutan', 'asc');
                },
                'approvals.approver',

                // --- [PERBAIKAN] Tambahkan relasi "anak" ---
                'permitGwp',
                'permitCse',
                'permitHwp',
                'permitEwp',
                'permitLp',
                // --- Selesai Perbaikan ---
            ])
            ->get();

        // 2. Filter di sisi server: Tampilkan HANYA jika SAYA adalah approver berikutnya
        $myTasks = $pendingPermits->filter(function ($workPermit) use ($userId) {
            if ($workPermit->approvals->isEmpty()) {
                return false; // Seharusnya tidak terjadi
            }

            // Ambil langkah approval pertama yang statusnya '0' (Pending)
            $nextApprovalStep = $workPermit->approvals->first();

            // Cek apakah SAYA adalah approver untuk langkah tersebut
            return $nextApprovalStep->approver_id == $userId;
        });

        // Kembalikan data yang sudah bersih
        return response()->json(['success' => true, 'data' => $myTasks->values()]);
    }

    /**
     * Menyetujui sebuah langkah persetujuan
     * $id di sini adalah ID dari 'work_permit_approval'
     */
    public function approve(Request $request, $id)
    {
        $request->validate(['catatan' => 'nullable|string|max:1000']);

        DB::beginTransaction();
        try {
            $approval   = WorkPermitApproval::findOrFail($id);
            $workPermit = $approval->workPermit;
            $userId     = Auth::id();

            if ($approval->approver_id !== $userId || $approval->status_persetujuan != 0) {
                return response()->json(['success' => false, 'error' => 'Aksi tidak diizinkan.'], 403);
            }

            $approval->update([
                'status_persetujuan' => 1, // 1 = Disetujui
                'tgl_persetujuan'    => Carbon::now(),
                'catatan'            => $request->input('catatan', 'Disetujui'),
            ]);

            $nextStep = WorkPermitApproval::where('work_permit_id', $workPermit->id)
                ->where('status_persetujuan', 0)
                ->orderBy('urutan', 'asc')
                ->first();

            if (! $nextStep) {
                $workPermit->update([
                    'status' => 3, // 3 = Disetujui (Aktif)
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Persetujuan berhasil dicatat.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menolak sebuah langkah persetujuan
     * $id di sini adalah ID dari 'work_permit_approval'
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string|min:5|max:1000']);

        DB::beginTransaction();
        try {
            $approval   = WorkPermitApproval::findOrFail($id);
            $workPermit = $approval->workPermit;
            $userId     = Auth::id();

            if ($approval->approver_id !== $userId || $approval->status_persetujuan != 0) {
                return response()->json(['success' => false, 'error' => 'Aksi tidak diizinkan.'], 403);
            }

            $approval->update([
                'status_persetujuan' => 2, // 2 = Ditolak
                'tgl_persetujuan'    => Carbon::now(),
                'catatan'            => $request->input('catatan'),
            ]);

            $workPermit->update([
                'status' => 4, // 4 = Ditolak
            ]);

            WorkPermitApproval::where('work_permit_id', $workPermit->id)
                ->where('status_persetujuan', 0)
                ->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Izin Kerja telah ditolak.']);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
