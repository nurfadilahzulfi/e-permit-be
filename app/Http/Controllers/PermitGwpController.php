<?php
namespace App\Http\Controllers;

// --- MODEL & LIBRARY YANG DIPERLUKAN ---\
use App\Models\GwpAlatLs;
use App\Models\GwpCek;
use App\Models\GwpCekHseLs;
use App\Models\GwpCekPemohonLs;
use App\Models\PermitGwp;
use App\Models\PermitGwpApproval;
use App\Models\PermitType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PermitGwpController extends Controller
{
    /**
     * [TETAP] Menampilkan halaman frontend (UI)
     */
    public function view(): View
    {
        return view('permit-gwp.index');
    }

    /**
     * [TETAP] Mengembalikan data JSON (Sudah difilter per pemohon)
     */
    public function index()
    {
        $user  = Auth::user();
        $query = PermitGwp::with(['approvals', 'pemohon', 'supervisor']);

        if ($user->role === 'pemohon') {
            // Pemohon hanya lihat miliknya
            $query->where('pemohon_id', $user->id);
        }

        $data = $query->latest()->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * [DIUBAH] Logika untuk menyimpan izin baru
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // 1. Validasi data
            $validated = $request->validate([
                'supervisor_id'       => 'required|integer|exists:user,id',
                'shift_kerja'         => 'required|string|max:20',
                'lokasi'              => 'required|string|max:255',
                'deskripsi_pekerjaan' => 'required|string',
                'peralatan_pekerjaan' => 'required|string',
            ]);

            $now = Carbon::now();

            // 2. [TETAP] Cari ID untuk "GWP" secara otomatis
            $gwpType = PermitType::where('kode', 'GWP')->first();
            if (! $gwpType) {
                throw ValidationException::withMessages(['permit_type_id' => 'Master data "GWP" tidak ditemukan.']);
            }

            // 3. [TETAP] Generate Nomor Izin Otomatis
            $bulan       = $now->format('m');
            $bulanRomawi = $this->getBulanRomawi($bulan);
            $tahun       = $now->format('Y');

            $countThisMonth = PermitGwp::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->count();

            $nomorRevisi = str_pad($countThisMonth + 1, 3, '0', STR_PAD_LEFT);
            $nomorIzin   = "{$nomorRevisi}/INL/HSE/{$bulanRomawi}/{$tahun}";

            // 4. Siapkan data untuk disimpan
            $dataToSave = $validated + [
                'pemohon_id'       => Auth::id(),
                'tgl_permohonan'   => $now,
                'status'           => 1, // Status 1 = Pending (Menunggu urutan 1, yaitu HSE)
                'nomor'            => $nomorIzin,
                'permit_type_id'   => $gwpType->id,
                'permit_type_kode' => $gwpType->kode,
            ];

            // 5. Simpan Izin GWP (Permit Utama)
            $permit = PermitGwp::create($dataToSave);

            // 6. Buat log persetujuan (approval)
            // =================================================================
            // !!! INI PERBAIKANNYA (Alur Dibalik Sesuai Flow Anda) !!!
            // =================================================================

            // 6a. Buat log untuk SEMUA user HSE (Urutan 1)
            // Sesuai Langkah 5 di flow Anda: "HSE megecek"
            $hseUsers = User::where('role', 'hse')->get();
            foreach ($hseUsers as $hse) {
                PermitGwpApproval::create([
                    'permit_gwp_id'      => $permit->id,
                    'approver_id'        => $hse->id,
                    'role_persetujuan'   => 'hse',
                    'urutan'             => 1, // <-- BENAR: HSE jadi urutan 1
                    'status_persetujuan' => 0,
                ]);
            }

            // 6b. Untuk Supervisor (pemilik lokasi) (Urutan 2)
            // Sesuai Langkah 6 di flow Anda: "Supervisor akan memberikan persetujuan"
            PermitGwpApproval::create([
                'permit_gwp_id'      => $permit->id,
                'approver_id'        => $validated['supervisor_id'],
                'role_persetujuan'   => 'supervisor',
                'urutan'             => 2, // <-- BENAR: Supervisor jadi urutan 2
                'status_persetujuan' => 0,
            ]);
            // =================================================================
            // AKHIR DARI PERBAIKAN
            // =================================================================

            // 7. Buat lembar checklist kosong (GwpCek)
            $this->createEmptyChecklists($permit->id, $now);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Izin GWP berhasil dibuat.', 'data' => $permit], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * [TETAP] Menampilkan detail satu Izin GWP
     */
    public function show($id)
    {
        $data = PermitGwp::with(['approvals.approver', 'pemohon', 'supervisor'])->find($id);

        if (! $data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * [TETAP] Update Izin GWP
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = PermitGwp::findOrFail($id);

            // Otorisasi
            if (Auth::id() !== $data->pemohon_id && Auth::user()->role !== 'admin') {
                return response()->json(['success' => false, 'error' => 'Anda tidak berhak mengedit izin ini.'], 403);
            }

            // Hanya izinkan edit jika status masih Draft (0) atau Rejected (4)
            if ($data->status !== 0 && $data->status !== 4) {
                return response()->json(['success' => false, 'error' => 'Izin yang sedang diproses tidak dapat diedit.'], 422);
            }

            // Validasi data (Mirip dengan store)
            $validated = $request->validate([
                'supervisor_id'       => 'required|integer|exists:user,id',
                'shift_kerja'         => 'required|string|max:20',
                'lokasi'              => 'required|string|max:255',
                'deskripsi_pekerjaan' => 'required|string',
                'peralatan_pekerjaan' => 'required|string',
            ]);

            // Saat mengedit, reset status kembali ke 'Pending HSE'
            $validated['status'] = 1;

            $data->update($validated);

            // (CATATAN: Jika supervisor diganti, kita harus menghapus approval lama
            // dan membuat yang baru. Logika ini bisa ditambahkan jika diperlukan)

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Izin GWP berhasil diperbarui.', 'data' => $data]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * [TETAP] Hapus Izin GWP
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = PermitGwp::findOrFail($id);

            if (Auth::id() !== $data->pemohon_id && Auth::user()->role !== 'admin') {
                return response()->json(['success' => false, 'error' => 'Anda tidak berhak menghapus izin ini.'], 403);
            }

            $data->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Izin GWP berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * [TETAP] Fungsi private untuk membuat checklist kosong
     */
    private function createEmptyChecklists($permit_gwp_id, $now)
    {
        $dataToInsert = [];
        $pemohonLs    = GwpCekPemohonLs::all();
        foreach ($pemohonLs as $item) {
            $dataToInsert[] = [
                'permit_gwp_id' => $permit_gwp_id, 'model' => GwpCekPemohonLs::class,
                'ls_id'         => $item->id, 'value'      => false,
                'created_at'    => $now, 'updated_at'      => $now,
            ];
        }

        $hseLs = GwpCekHseLs::all();
        foreach ($hseLs as $item) {
            $dataToInsert[] = [
                'permit_gwp_id' => $permit_gwp_id, 'model' => GwpCekHseLs::class,
                'ls_id'         => $item->id, 'value'      => false,
                'created_at'    => $now, 'updated_at'      => $now,
            ];
        }

        $alatLs = GwpAlatLs::all();
        foreach ($alatLs as $item) {
            $dataToInsert[] = [
                'permit_gwp_id' => $permit_gwp_id, 'model' => GwpAlatLs::class,
                'ls_id'         => $item->id, 'value'      => false,
                'created_at'    => $now, 'updated_at'      => $now,
            ];
        }

        if (! empty($dataToInsert)) {
            GwpCek::insert($dataToInsert);
        }
    }

    /**
     * [TETAP] Helper function untuk mengubah bulan numerik ke romawi
     */
    private function getBulanRomawi($bulan)
    {
        $romawi = [
            '01' => 'I', '02'   => 'II', '03'   => 'III', '04' => 'IV', '05' => 'V', '06'  => 'VI',
            '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10'  => 'X', '11'  => 'XI', '12' => 'XII',
        ];
        return $romawi[$bulan] ?? $bulan;
    }
}
