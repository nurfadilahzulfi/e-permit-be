<?php
namespace App\Http\Controllers;

use App\Models\PermitGwp;
use Illuminate\Http\Request;

class PermitGwpController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CATATAN PERUBAHAN
    |--------------------------------------------------------------------------
    |
    | Fungsi view() dan index() telah DIHAPUS dari controller ini.
    | Alasan: Logika daftar izin kini ditangani oleh WorkPermitController.
    |
    */

    /**
     * [USANG] Fungsi ini tidak lagi dipakai.
     */
    public function store(Request $request)
    {
        return response()->json(['success' => false, 'error' => 'Metode ini tidak digunakan.'], 405);
    }

    /**
     * [TETAP] Menampilkan detail spesifik GWP.
     */
    public function show($id)
    {
        $data = PermitGwp::with(['workPermit.pemohon', 'workPermit.supervisor'])
            ->find($id);

        if (! $data) {
            return response()->json(['success' => false, 'message' => 'Data GWP tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }
}
