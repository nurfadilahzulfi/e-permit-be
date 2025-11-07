<?php
namespace App\Http\Controllers;

use App\Models\CseCek;
use App\Models\PermitCse; // <-- DIUBAH
// use App\Models\WorkPermit; // (Sudah ada di GwpCekController)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CseCekController extends Controller
{
    /**
     * Menampilkan halaman HTML (Blade) untuk Checklist CSE.
     */
    public function viewChecklistPage($permit_cse_id) // <-- DIUBAH
    {
                                                                                        // 1. Ambil data CSE (anak) beserta relasi ke induknya (workPermit)
        $permitCse = PermitCse::with('workPermit.pemohon')->findOrFail($permit_cse_id); // <-- DIUBAH

        // 2. Ambil data induknya
        $workPermit = $permitCse->workPermit;

                                       // 3. Kirim KEDUA data (induk & anak) ke view
        return view('cse-cek.index', [ // <-- DIUBAH
            'permitCse'  => $permitCse,    // <-- DIUBAH
            'workPermit' => $workPermit,
        ]);
    }

    /**
     * Mengambil DATA (JSON) semua item checklist
     */
    public function index($permit_cse_id) // <-- DIUBAH
    {
        $data = CseCek::where('permit_cse_id', $permit_cse_id) // <-- DIUBAH
            ->with('ls')
            ->get();

        // Mengelompokkan berdasarkan 'model'
        $grouped = $data->groupBy('model')->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'id'    => $item->id,
                    'nama'  => $item->ls ? $item->ls->nama : 'N/A',
                    'value' => (bool) $item->value,
                ];
            });
        });

        // Pastikan semua grup ada
        $result = [
            // <-- DIUBAH
            'persiapan' => $grouped->get('App\Models\CseCekPersiapanLs', []),
            'gas'       => $grouped->get('App\Models\CseCekGasLs', []),
        ];

        return response()->json(['success' => true, 'data' => $result]);
    }

    /**
     * Memperbarui satu item checklist (true/false).
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = CseCek::findOrFail($id); // <-- DIUBAH

            $validated = $request->validate(['value' => 'required|boolean']);
            $data->update($validated);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Checklist item diperbarui.', 'data' => $data]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
