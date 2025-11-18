<?php
namespace App\Http\Controllers;

use App\Models\EwpCek;
use App\Models\PermitEwp; // Model "Anak" EWP
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EwpCekController extends Controller
{
    /**
     * Menampilkan halaman HTML (Blade) untuk Checklist EWP.
     */
    public function viewChecklistPage($permit_ewp_id)
    {
        // 1. Ambil data EWP (anak) beserta relasi ke induknya (workPermit)
        $permitEwp = PermitEwp::with('workPermit.pemohon')->findOrFail($permit_ewp_id);

        // 2. Ambil data induknya
        $workPermit = $permitEwp->workPermit;

                                       // 3. Kirim KEDUA data (induk & anak) ke view
        return view('ewp-cek.index', [ // resources/views/ewp-cek/index.blade.php
            'permitEwp'  => $permitEwp,
            'workPermit' => $workPermit,
        ]);
    }

    /**
     * Mengambil DATA (JSON) semua item checklist EWP
     */
    public function index($permit_ewp_id)
    {
        $data = EwpCek::where('permit_ewp_id', $permit_ewp_id)
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
            'ewp_items' => $grouped->get('App\\Models\\EwpCekLs', []),
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
            $data      = EwpCek::findOrFail($id);
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
