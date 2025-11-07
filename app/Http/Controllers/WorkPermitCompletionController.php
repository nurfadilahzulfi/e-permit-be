<?php
namespace App\Http\Controllers;

use App\Models\WorkPermit;
use App\Models\WorkPermitCompletion;
use Carbon\Carbon;
use Illuminate\Contracts\View\View; // <-- [KURANG] Anda kekurangan 'use' statement ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkPermitCompletionController extends Controller
{
    /**
     * [LANGKAH 7 - SUDAH ADA] Dipanggil oleh Pemohon (Tombol "Pekerjaan Selesai").
     */
    public function startCompletion(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $workPermit = WorkPermit::findOrFail($id);

            // 1. Otorisasi: Pastikan user adalah pemohon & status "Approved"
            if (Auth::id() !== $workPermit->pemohon_id) {
                return response()->json(['success' => false, 'error' => 'Hanya pemohon yang dapat menyelesaikan pekerjaan.'], 403);
            }
            if ($workPermit->status !== 3) {
                return response()->json(['success' => false, 'error' => 'Izin kerja ini belum disetujui.'], 422);
            }

            $workPermit->status = 5; // 5 = Menunggu Penutupan HSE
            $workPermit->save();

            // 3. Buat 3 Log Penutupan (Sesuai Langkah 8, 10, 11)

            // Log 1: HSE (Urutan 1) - Sesuai Langkah 8
            WorkPermitCompletion::create([
                'work_permit_id'   => $workPermit->id,
                'user_id'          => $workPermit->hse_id, // HSE yg inisiasi
                'role_penutupan'   => 'hse',
                'urutan'           => 1,
                'status_penutupan' => 0, // Pending
            ]);

            // Log 2: Supervisor (Urutan 2) - Sesuai Langkah 10
            WorkPermitCompletion::create([
                'work_permit_id'   => $workPermit->id,
                'user_id'          => $workPermit->supervisor_id,
                'role_penutupan'   => 'supervisor',
                'urutan'           => 2,
                'status_penutupan' => 0, // Pending
            ]);

            // Log 3: Pemohon (Urutan 3) - Sesuai Langkah 11
            WorkPermitCompletion::create([
                'work_permit_id'   => $workPermit->id,
                'user_id'          => $workPermit->pemohon_id,
                'role_penutupan'   => 'pemohon',
                'urutan'           => 3,
                'status_penutupan' => 0, // Pending
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pekerjaan telah ditandai selesai. Menunggu pengesahan dari HSE.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ========================================================
    // [BARU] FUNGSI-FUNGSI YANG HILANG DARI FILE ANDA
    // ========================================================

    /**
     * [BARU] Menampilkan halaman (View) untuk list "Pengesahan Selesai".
     */
    public function view(): View
    {
        return view('work-permit-completion.index');
    }

    /**
     * [BARU] Mengambil data (JSON) untuk list tugas penutupan.
     */
    public function index()
    {
        $userId = Auth::id();

                                                         // Ambil data izin kerja (Induk) yang status penutupannya relevan
        $data = WorkPermit::whereIn('status', [5, 6, 7]) // 5=HSE, 6=SPV, 7=Pemohon
            ->whereHas('completions', function ($query) use ($userId) {
                                                  // Filter:
                $query->where('user_id', $userId) // 1. Tugas ini untuk saya
                    ->where('status_penutupan', 0);   // 2. Yang masih pending
            })
            ->with(['completions' => function ($query) use ($userId) {
                // Ambil HANYA log penutupan yang relevan untuk user ini
                $query->where('user_id', $userId)->where('status_penutupan', 0);
            }, 'pemohon', 'supervisor', 'hse'])
            ->get();

        // Format data untuk dikirim ke frontend
        $formattedData = $data->map(function ($workPermit) {
            $myCompletionTask = $workPermit->completions->first();
            if (! $myCompletionTask) {
                return null;
            }

            // Cek apakah giliran saya?
            // (Status 5 = Urutan 1, Status 6 = Urutan 2, Status 7 = Urutan 3)
            $isMyTurn = ($workPermit->status === (4 + $myCompletionTask->urutan));

            return [
                'completion_id'       => $myCompletionTask->id, // ID untuk aksi 'sign'
                'work_permit_id'      => $workPermit->id,
                'nomor_pekerjaan'     => $workPermit->nomor_pekerjaan,
                'deskripsi_pekerjaan' => $workPermit->deskripsi_pekerjaan,
                'lokasi'              => $workPermit->lokasi,
                'pemohon'             => $workPermit->pemohon ? $workPermit->pemohon->nama : 'N/A',
                'tahap'               => $myCompletionTask->role_penutupan,
                'urutan'              => $myCompletionTask->urutan,
                'is_my_turn'          => $isMyTurn, // true/false
            ];
        })->filter(); // Hapus null jika ada

        return response()->json(['success' => true, 'data' => $formattedData]);
    }

    /**
     * [BARU] Aksi untuk "Tanda Tangan" pengesahan selesai.
     */
    public function signCompletion(Request $request)
    {
        $request->validate([
            'completion_id' => 'required|integer|exists:work_permit_completions,id',
            'catatan'       => 'nullable|string|max:500',
        ]);

        $completionId = $request->input('completion_id');
        $userId       = Auth::id();

        DB::beginTransaction();
        try {
            $completionLog = WorkPermitCompletion::findOrFail($completionId);
            $workPermit    = WorkPermit::findOrFail($completionLog->work_permit_id);

            // 1. Otorisasi: Pastikan ini tugas user
            if ($userId !== $completionLog->user_id || $completionLog->status_penutupan !== 0) {
                return response()->json(['success' => false, 'error' => 'Aksi tidak diizinkan atau sudah diproses.'], 403);
            }

            // 2. Otorisasi: Pastikan ini giliran user
            if ($workPermit->status !== (4 + $completionLog->urutan)) {
                return response()->json(['success' => false, 'error' => 'Bukan giliran Anda untuk menandatangani.'], 422);
            }

                                                  // 3. Update Log Penutupan
            $completionLog->status_penutupan = 1; // 1 = Signed
            $completionLog->tgl_penutupan    = Carbon::now();
            $completionLog->catatan          = $request->input('catatan');
            $completionLog->save();

            // 4. Update Status Induk
            if ($completionLog->urutan === 3) {
                                         // Ini urutan terakhir (Pemohon), tutup Izin Kerja
                $workPermit->status = 8; // 8 = Closed / Arsip
            } else {
                // Maju ke urutan berikutnya (misal: dari 5->6, 6->7)
                $workPermit->status = $workPermit->status + 1;
            }
            $workPermit->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pengesahan selesai berhasil dicatat.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
