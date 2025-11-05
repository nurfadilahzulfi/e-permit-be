<?php
namespace App\Http\Controllers;

use App\Models\GwpCek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GwpCekController extends Controller
{
    /**
     * [LOGIC BARU] Menampilkan semua item checklist (lembar jawaban)
     * untuk satu Permit GWP tertentu.
     */
    public function index($permit_gwp_id) // Parameter dari route
    {
        // Ambil semua 'jawaban' untuk permit ini
        // 'ls' adalah relasi MorphTo yang kita definisikan di Model GwpCek
        // Ini akan otomatis mengambil "pertanyaan" dari tabel master yang benar
        $data = GwpCek::where('permit_gwp_id', $permit_gwp_id)
            ->with('ls')
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Checklist tidak ditemukan atau belum dibuat untuk permit ini.'], 404);
        }

        // Kelompokkan berdasarkan 'model' (Pemohon, HSE, Alat)
        $groupedData = $data->groupBy('model');

        return response()->json(['success' => true, 'data' => $groupedData]);
    }

    /**
     * [LOGIC BARU] Update satu item checklist (mencentang kotak).
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

    // --- FUNGSI LAMA TIDAK DIPERLUKAN LAGI ---

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
