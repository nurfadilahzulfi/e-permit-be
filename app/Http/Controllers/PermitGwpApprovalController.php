<?php
namespace App\Http\Controllers;

// --- MODEL & LIBRARY YANG DIPERLUKAN ---
use App\Models\PermitGwp;
use App\Models\PermitGwpApproval;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermitGwpApprovalController extends Controller
{
    /**
     * Menampilkan daftar izin yang PERLU PERSETUJUAN SAYA (Supervisor atau HSE)
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
            ->with('approver') // Tampilkan data user yang approve
            ->orderBy('created_at', 'asc')
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Histori approval tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * FUNGSI BARU: Untuk MENYETUJUI izin
     * (Logic "Supervisor Approve" & "HSE Approve")
     *
     * @param int $permit_gwp_id
     */
    public function approve(Request $request, $permit_gwp_id)
    {
        $user = Auth::user(); // User yang sedang login (Approver)

        DB::beginTransaction();
        try {
            // 1. Cari izin utamanya
            $permit = PermitGwp::findOrFail($permit_gwp_id);

            // 2. Cari log persetujuan yang pending untuk user ini
            $approvalLog = PermitGwpApproval::where('permit_gwp_id', $permit->id)
                ->where('approver_id', $user->id)
                ->where('status_persetujuan', 0) // 0 = Pending
                ->firstOrFail();

            // 3. Update log persetujuan ini
            $approvalLog->update([
                'status_persetujuan' => 1, // 1 = Approved
                'tgl_persetujuan'    => Carbon::now(),
                'catatan'            => $request->catatan ?? 'Disetujui',
            ]);

            // 4. LOGIC INTI: Tentukan langkah selanjutnya berdasarkan Role

            if ($approvalLog->role_persetujuan == 'SUPERVISOR') {

                // --- Alur Supervisor Selesai, Lanjut ke HSE ---

                // 4a. Update status global izin ke "Pending HSE" (Status 2)
                $permit->update(['status' => 2]);

                // 4b. Cari user HSE (Kita ambil user pertama dengan jabatan 'HSE')
                // PERHATIAN: Ini asumsi. Jika ada logic lain, ganti di sini.
                $hseUser = User::where('jabatan', 'HSE')->first();

                if (! $hseUser) {
                    // Jika tidak ada user HSE, batalkan transaksi
                    throw new \Exception('User HSE tidak ditemukan untuk persetujuan berikutnya.');
                }

                // 4c. Buat log persetujuan baru untuk HSE
                PermitGwpApproval::create([
                    'permit_gwp_id'      => $permit->id,
                    'approver_id'        => $hseUser->id,
                    'role_persetujuan'   => 'HSE',
                    'status_persetujuan' => 0, // 0 = Pending
                ]);

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Izin disetujui. Diteruskan ke HSE.']);

            } elseif ($approvalLog->role_persetujuan == 'HSE') {

                // --- Alur HSE Selesai, Izin Diterbitkan ---

                // 4a. Update status global izin ke "Approved" (Status 3)
                // 4b. Set masa berlaku 2 minggu (sesuai flowchart)
                $permit->update([
                    'status'      => 3,
                    'valid_until' => Carbon::now()->addWeeks(2),
                ]);

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Izin berhasil disetujui dan diterbitkan.']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * FUNGSI BARU: Untuk MENOLAK izin
     * (Logic "Supervisor Reject" & "HSE Reject")
     *
     * @param int $permit_gwp_id
     */
    public function reject(Request $request, $permit_gwp_id)
    {
        $user = Auth::user(); // User yang sedang login (Approver)

        // Validasi: Catatan wajib diisi jika ditolak
        $request->validate(['catatan' => 'required|string|min:10']);

        DB::beginTransaction();
        try {
            // 1. Cari izin utamanya
            $permit = PermitGwp::findOrFail($permit_gwp_id);

            // 2. Cari log persetujuan yang pending untuk user ini
            $approvalLog = PermitGwpApproval::where('permit_gwp_id', $permit->id)
                ->where('approver_id', $user->id)
                ->where('status_persetujuan', 0) // 0 = Pending
                ->firstOrFail();

            // 3. Update log persetujuan ini
            $approvalLog->update([
                'status_persetujuan' => 2, // 2 = Rejected
                'tgl_persetujuan'    => Carbon::now(),
                'catatan'            => $request->catatan,
            ]);

            // 4. Update status global izin ke "Rejected" (Status 4)
            $permit->update(['status' => 4]);

            // Alur selesai, tidak perlu buat log baru

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
            return response()->json(['success' => true, 'message' => 'Histori approval berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
