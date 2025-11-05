<?php
namespace App\Http\Controllers;

// --- MODEL & LIBRARY YANG DIPERLUKAN ---
use App\Models\GwpAlatLs;
use App\Models\GwpCek;
use App\Models\GwpCekHseLs;
// --- TAMBAHAN BARU UNTUK CHECKLIST ---
use App\Models\GwpCekPemohonLs;
use App\Models\PermitGwp;
use App\Models\PermitGwpApproval;
use App\Models\User;
// ---
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PermitGwpController extends Controller
{
    // ... (fungsi index() dan show() kamu biarkan saja) ...

    public function index()
    {
        // $data = PermitGwp::with(['approvals', 'completions'])->get();

        // Versi lebih baik: Tampilkan juga data pemohon dan supervisor
        $data = PermitGwp::with([
            'pemohon',    // Asumsi ada relasi 'pemohon' di model PermitGwp
            'supervisor', // Asumsi ada relasi 'supervisor' di model PermitGwp
            'approvals' => function ($q) {
                $q->with('approver'); // Asumsi ada relasi 'approver' di model PermitGwpApproval
            },
        ])->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show($id)
    {
        $data = PermitGwp::with(['approvals', 'completions', 'pemohon', 'supervisor'])->find($id);

        if (! $data) {
            return response()->json(['success' => false, 'message' => 'Permit tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * [DIPERBARUI] Menyimpan (Submit) Permit GWP baru DAN MEMBUAT CHECKLIST
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'permit_type_id'      => 'required|integer|exists:permit_types,id',
                'nomor'               => 'required|string|unique:permit_gwp,nomor',
                'shift_kerja'         => 'required|string|max:20',
                'lokasi'              => 'required|string|max:255',
                'deskripsi_pekerjaan' => 'required|string',
                'peralatan_pekerjaan' => 'required|string',
                'pemohon_jenis'       => 'required|in:internal,eksternal',
                'supervisor_id'       => 'required|integer|exists:user,id',
            ]);

            $validated['pemohon_id']     = Auth::id();
            $validated['tgl_permohonan'] = Carbon::now();
            $validated['status']         = 1; // Langsung ke 'Pending Supervisor'

            // 1. Simpan data Izin GWP utama
            $permit = PermitGwp::create($validated);

            // 2. Buat log persetujuan pertama (Untuk Supervisor)
            PermitGwpApproval::create([
                'permit_gwp_id'      => $permit->id,
                'approver_id'        => $request->supervisor_id,
                'role_persetujuan'   => 'SUPERVISOR',
                'status_persetujuan' => 0, // 0 = Pending
            ]);

            // --- LOGIC CHECKLIST BARU ---
            // 3. Panggil fungsi private untuk auto-populate checklist
            $this->createChecklistForPermit($permit->id);
            // --- AKHIR LOGIC CHECKLIST BARU ---

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Permit berhasil diajukan dan menunggu persetujuan Supervisor.', 'data' => $permit], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * [DIPERBARUI] Update data Permit GWP
     */
    public function update(Request $request, $id)
    {
        $permit = PermitGwp::findOrFail($id);

        // Hanya izinkan update jika status masih Draft (0) atau Rejected (4)
        if (! in_array($permit->status, [0, 4])) {
            return response()->json(['success' => false, 'error' => 'Izin tidak dapat diubah karena sedang dalam proses persetujuan.'], 403);
        }

        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'permit_type_id'      => 'required|integer|exists:permit_types,id',
                'nomor'               => 'required|string|unique:permit_gwp,nomor,' . $id,
                'shift_kerja'         => 'required|string|max:20',
                'lokasi'              => 'required|string|max:255',
                'deskripsi_pekerjaan' => 'required|string',
                'peralatan_pekerjaan' => 'required|string',
                'pemohon_jenis'       => 'required|in:internal,eksternal',
                'supervisor_id'       => 'required|integer|exists:user,id',
            ]);

            $validated['status'] = 1; // Set status kembali ke 'Pending Supervisor'
            $permit->update($validated);

            // Hapus log approval lama dan checklist lama
            PermitGwpApproval::where('permit_gwp_id', $permit->id)->delete();
            GwpCek::where('permit_gwp_id', $permit->id)->delete(); // Hapus checklist lama

            // Buat log approval baru
            PermitGwpApproval::create([
                'permit_gwp_id'      => $permit->id,
                'approver_id'        => $request->supervisor_id,
                'role_persetujuan'   => 'SUPERVISOR',
                'status_persetujuan' => 0, // Pending
            ]);

            // Buat ulang checklist
            $this->createChecklistForPermit($permit->id);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Permit berhasil diperbarui dan diajukan ulang.', 'data' => $permit]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus Permit GWP
     */
    public function destroy($id)
    {
        // (Biarkan fungsi destroy kamu apa adanya, 'onDelete('cascade')' akan
        // otomatis menghapus semua approval dan checklist)
        DB::beginTransaction();
        try {
            $permit = PermitGwp::findOrFail($id);
            $permit->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Permit berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * [FUNGSI BARU] Auto-populate lembar jawaban (gwp_cek)
     * berdasarkan semua data master.
     */
    private function createChecklistForPermit($permit_gwp_id)
    {
        $dataToInsert = [];
        $now          = Carbon::now();

        // 1. Ambil semua master checklist Pemohon
        $pemohonLs = GwpCekPemohonLs::all();
        foreach ($pemohonLs as $item) {
            $dataToInsert[] = [
                'permit_gwp_id' => $permit_gwp_id,
                'model'         => GwpCekPemohonLs::class, // Nama Model (Polymorphic)
                'ls_id'         => $item->id,              // ID dari master
                'value'         => false,                  // Default (belum dicentang)
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // 2. Ambil semua master checklist HSE
        $hseLs = GwpCekHseLs::all();
        foreach ($hseLs as $item) {
            $dataToInsert[] = [
                'permit_gwp_id' => $permit_gwp_id,
                'model'         => GwpCekHseLs::class,
                'ls_id'         => $item->id,
                'value'         => false,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // 3. Ambil semua master checklist Alat
        $alatLs = GwpAlatLs::all();
        foreach ($alatLs as $item) {
            $dataToInsert[] = [
                'permit_gwp_id' => $permit_gwp_id,
                'model'         => GwpAlatLs::class,
                'ls_id'         => $item->id,
                'value'         => false,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // 4. Masukkan semua data ke tabel 'gwp_cek' sekaligus
        if (! empty($dataToInsert)) {
            GwpCek::insert($dataToInsert);
        }
    }
}
