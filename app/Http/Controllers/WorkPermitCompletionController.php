<?php
namespace App\Http\Controllers;

use App\Models\WorkPermit;
use App\Models\WorkPermitCompletion;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkPermitCompletionController extends Controller
{
    /**
     * Menampilkan halaman (View) "Pengesahan Selesai".
     */
    public function view(): View
    {
        return view('work-permit-completion.index');
    }

    /**
     * [FLOWCHART L7] Dipanggil Pemohon saat klik "Pekerjaan Selesai".
     * $id adalah ID dari WorkPermit.
     */
    public function startCompletion(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $workPermit = WorkPermit::findOrFail($id);
            $user       = Auth::user();

            // Otorisasi: Hanya pemohon dan status harus 3 (Aktif)
            if ($workPermit->pemohon_id !== $user->id || $workPermit->status !== 3) {
                return response()->json(['success' => false, 'error' => 'Aksi tidak diizinkan.'], 403);
            }

            // Ubah status ke 5 (Pending Penutupan Pemohon)
            $workPermit->update(['status' => 5]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Izin kerja telah ditandai untuk penutupan. Silakan isi form penutupan.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mengembalikan data JSON untuk daftar "Pengesahan Selesai".
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil semua izin yang statusnya sedang dalam alur penutupan
        $query = WorkPermit::whereIn('status', [5, 6, 7])
            ->with(['pemohon', 'supervisor', 'hse', 'completions.user']);

        $permits = $query->latest()->get();

        // Filter di server agar user hanya melihat tugasnya
        $myTasks = $permits->filter(function ($permit) use ($user) {
            // Status 5: Tugas Pemohon
            if ($permit->status == 5 && $user->role === 'pemohon') {
                return true;
            }

            // Status 6: Tugas HSE
            if ($permit->status == 6 && $user->role === 'hse') {
                return true;
            }

            // Status 7: Tugas Supervisor
            if ($permit->status == 7 && $user->role === 'supervisor') {
                return true;
            }

            return false;
        });

        return response()->json(['success' => true, 'data' => $myTasks->values()]);
    }

    /**
     * [FLOWCHART L8, 9, 10] Dipanggil saat Pemohon/HSE/Supervisor menekan tombol "Sign".
     */
    public function signCompletion(Request $request)
    {
        $validated = $request->validate([
            'work_permit_id' => 'required|integer|exists:work_permits,id',
            'catatan'        => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $user       = Auth::user();
            $workPermit = WorkPermit::findOrFail($validated['work_permit_id']);
            $role       = $user->role;
            $status     = $workPermit->status;

            $nextStatus = null;
            $logRole    = null;

                                                       // Tentukan alur (state machine)
            if ($status == 5 && $role === 'pemohon') { // [L8] Pemohon TTD
                $nextStatus = 6;                           // Lanjut ke HSE
                $logRole    = 'pemohon';
            } else if ($status == 6 && $role === 'hse') { // [L9] HSE TTD
                $nextStatus = 7;                              // Lanjut ke Supervisor
                $logRole    = 'hse';
            } else if ($status == 7 && $role === 'supervisor') { // [L10] SPV TTD
                $nextStatus = 8;                                     // Selesai / Arsip
                $logRole    = 'supervisor';
            } else {
                // Jika user mencoba sign di luar gilirannya
                return response()->json(['success' => false, 'error' => 'Aksi tidak diizinkan atau bukan giliran Anda.'], 403);
            }

            // 1. Buat log "Tanda Tangan"
            WorkPermitCompletion::create([
                'work_permit_id' => $workPermit->id,
                'user_id'        => $user->id,
                'role_penutupan' => $logRole,
                'tgl_penutupan'  => Carbon::now(),
                'catatan'        => $validated['catatan'] ?? 'Pekerjaan telah diperiksa dan diselesaikan.',
            ]);

            // 2. Update status Izin Kerja Induk
            $workPermit->update(['status' => $nextStatus]);

            DB::commit();

            if ($nextStatus == 8) {
                return response()->json(['success' => true, 'message' => 'Izin Kerja telah berhasil ditutup dan diarsipkan.']);
            } else {
                return response()->json(['success' => true, 'message' => 'Penutupan berhasil dicatat, menunggu langkah berikutnya.']);
            }

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
