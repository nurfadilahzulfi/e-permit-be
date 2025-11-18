<?php
namespace App\Http\Controllers;

use App\Models\HwpCek;
use App\Models\PermitHwp; // Model "Anak" HWP
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HwpCekController extends Controller
{
    /**
     * Menampilkan halaman HTML (Blade) untuk Checklist HWP.
     */
    public function viewChecklistPage($permit_hwp_id)
    {
        // 1. Ambil data HWP (anak) beserta relasi ke induknya (workPermit)
        $permitHwp = PermitHwp::with('workPermit.pemohon')->findOrFail($permit_hwp_id);

        // 2. Ambil data induknya
        $workPermit = $permitHwp->workPermit;

                                       // 3. Kirim KEDUA data (induk & anak) ke view
        return view('hwp-cek.index', [ // resources/views/hwp-cek/index.blade.php
            'permitHwp'  => $permitHwp,
            'workPermit' => $workPermit,
        ]);
    }

    /**
     * Mengambil DATA (JSON) semua item checklist HWP
     */
    public function index($permit_hwp_id)
    {
        $data = HwpCek::where('permit_hwp_id', $permit_hwp_id)
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
            'hwp_items' => $grouped->get('App\\Models\\HwpCekLs', []),
            // Tambahkan grup lain jika ada (misal: 'hse', 'alat', dll)
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
            $data = HwpCek::findOrFail($id);

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
