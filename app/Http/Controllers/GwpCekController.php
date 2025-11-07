<?php
namespace App\Http\Controllers;

use App\Models\GwpCek;
use App\Models\PermitGwp;  // Model GWP (Anak)
use App\Models\WorkPermit; // [BARU] Model Induk
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GwpCekController extends Controller
{
    /**
     * [DIUBAH] Menampilkan halaman HTML (Blade) untuk Checklist.
     */
    public function viewChecklistPage($permit_gwp_id)
    {
        // 1. Ambil data GWP (anak) beserta relasi ke induknya (workPermit)
        $permitGwp = PermitGwp::with('workPermit.pemohon')->findOrFail($permit_gwp_id);

        // 2. Ambil data induknya
        $workPermit = $permitGwp->workPermit;

        // 3. Kirim KEDUA data (induk & anak) ke view
        return view('gwp-cek.index', [
            'permitGwp'  => $permitGwp,
            'workPermit' => $workPermit, // <-- [BARU] Kirim data induk
        ]);
    }

    /**
     * [TETAP] Mengambil DATA (JSON) semua item checklist
     */
    public function index($permit_gwp_id)
    {
        // 'ls' adalah relasi MorphTo
        $data = GwpCek::where('permit_gwp_id', $permit_gwp_id)
            ->with('ls')
            ->get();

        // Mengelompokkan berdasarkan 'model'
        $grouped = $data->groupBy('model')->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'id'    => $item->id,
                    'nama'  => $item->ls ? $item->ls->nama : 'N/A', // Ambil nama dari relasi 'ls'
                    'value' => (bool) $item->value,
                ];
            });
        });

        // Pastikan semua grup ada
        $result = [
            'pemohon' => $grouped->get('App\Models\GwpCekPemohonLs', []),
            'hse'     => $grouped->get('App\Models\GwpCekHseLs', []),
            'alat'    => $grouped->get('App\Models\GwpAlatLs', []),
        ];

        return response()->json(['success' => true, 'data' => $result]);
    }

    /**
     * [TETAP] Memperbarui satu item checklist (true/false).
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = GwpCek::findOrFail($id);

            // (Nanti tambahkan otorisasi: hanya pemohon/hse yg boleh ubah)

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
