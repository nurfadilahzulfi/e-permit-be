<?php
namespace App\Http\Controllers;

use App\Models\GwpAlatLs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GwpAlatLsController extends Controller
{
    public function index()
    {
        $data = GwpAlatLs::all();
        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => $data->isEmpty() ? 'Belum ada data GWP Alat' : 'Data GWP Alat ditemukan',
        ]);
    }

    public function show($id)
    {
        $data = GwpAlatLs::find($id);
        if (! $data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);

        DB::beginTransaction();
        try {
            $data = GwpAlatLs::create([
                'nama' => $request->nama,
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data GWP Alat berhasil disimpan',
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama' => 'required|string|max:255']);

        DB::beginTransaction();
        try {
            $data = GwpAlatLs::findOrFail($id);
            $data->update(['nama' => $request->nama]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data GWP Alat berhasil diperbarui',
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = GwpAlatLs::findOrFail($id);
            $data->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data GWP Alat berhasil dihapus',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
