<?php
namespace App\Http\Controllers;

use App\Models\GwpCek;
use App\Models\PermitGwp; // <-- 1. TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GwpCekController extends Controller
{
    /**
     * [FUNGSI BARU] Menampilkan halaman HTML (Blade) untuk Checklist.
     * Ini adalah halaman yang dilihat user di browser.
     */
    public function viewChecklistPage($permit_gwp_id)
    {
        // Ambil data izin utamanya untuk ditampilkan (misal: judul halaman)
        $permit = PermitGwp::findOrFail($permit_gwp_id);

        // Kirim data permit (termasuk ID-nya) ke view
        // Pastikan file view 'gwp-cek.index' ada
        return view('gwp-cek.index', [
            'permit' => $permit,
        ]);
    }

    /**
     * [LOGIC DIPERBAIKI] Mengambil DATA (JSON) semua item checklist
     * untuk satu Permit GWP tertentu. (Dipanggil oleh JavaScript)
     */
    public function index($permit_gwp_id) // Parameter dari route
    {
        // 'ls' adalah relasi MorphTo yang kita definisikan di Model GwpCek
        $data = GwpCek::where('permit_gwp_id', $permit_gwp_id)
            ->with('ls') // 'ls' adalah nama fungsi relasi di Model GwpCek
            ->get();

        if ($data->isEmpty()) {
            // Ini akan terjadi jika PermitGwpController@store belum membuat checklist
            return response()->json(['success' => false, 'message' => 'Checklist tidak ditemukan atau belum dibuat untuk permit ini.'], 404);
        }

        // Kelompokkan berdasarkan 'model' (Pemohon, HSE, Alat)
        $groupedData = $data->groupBy('model');

        return response()->json(['success' => true, 'data' => $groupedData]);
    }

    /**
     * [LOGIC DIPERBAIKI] Update satu item checklist (mencentang kotak).
     * $id di sini adalah ID dari tabel 'gwp_cek' (lembar jawaban).
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = GwpCek::findOrFail($id);

            // Validasi hanya 'value' (true/false)
            $validated = $request->validate([
                'value' => 'required|boolean',
            ]);

            $data->update($validated);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Checklist item diperbarui.', 'data' => $data]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // --- FUNGSI LAMA (STORE/DESTROY/SHOW) TIDAK DIPERLUKAN LAGI ---
    public function show($id)
    {
        return response()->json(['success' => false, 'error' => 'Metode tidak diizinkan.'], 405);
    }
    public function store(Request $request)
    {
        return response()->json(['success' => false, 'error' => 'Metode tidak diizinkan. Checklist dibuat otomatis.'], 405);
    }
    public function destroy($id)
    {
        return response()->json(['success' => false, 'error' => 'Metode tidak diizinkan. Checklist dihapus otomatis.'], 405);
    }
}
