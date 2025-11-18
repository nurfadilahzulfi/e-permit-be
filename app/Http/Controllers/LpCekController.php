<?php
namespace App\Http\Controllers;

use App\Models\LpCek;
use App\Models\PermitLp; // Model "Anak" LP
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LpCekController extends Controller
{
    /**
     * Menampilkan halaman HTML (Blade) untuk Checklist LP.
     */
    public function viewChecklistPage($permit_lp_id)
    {
        // 1. Ambil data LP (anak) beserta relasi ke induknya (workPermit)
        $permitLp = PermitLp::with('workPermit.pemohon')->findOrFail($permit_lp_id);

        // 2. Ambil data induknya
        $workPermit = $permitLp->workPermit;

                                      // 3. Kirim KEDUA data (induk & anak) ke view
        return view('lp-cek.index', [ // resources/views/lp-cek/index.blade.php
            'permitLp'   => $permitLp,
            'workPermit' => $workPermit,
        ]);
    }

    /**
     * Mengambil DATA (JSON) semua item checklist LP
     */
    public function index($permit_lp_id)
    {
        $data = LpCek::where('permit_lp_id', $permit_lp_id)
            ->with('ls') // 'ls' adalah relasi MorphTo
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

        // Pastikan semua grup ada (sesuaikan nama Model master kamu)
        $result = [
            'lp_items' => $grouped->get('App\\Models\\LpCekLs', []),
            // Tambahkan grup lain jika ada
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
            $data      = LpCek::findOrFail($id);
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
