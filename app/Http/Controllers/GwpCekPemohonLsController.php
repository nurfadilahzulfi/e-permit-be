<?php
namespace App\Http\Controllers;

use App\Models\GwpCekPemohonLs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GwpCekPemohonLsController extends Controller
{
    public function index()
    {
        $data = GwpCekPemohonLs::all();
        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => $data->isEmpty() ? 'Belum ada data GWP Cek Pemohon' : 'Data GWP Cek Pemohon ditemukan',
        ]);
    }

    public function show($id)
    {
        $data = GwpCekPemohonLs::find($id);
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
            $data = GwpCekPemohonLs::create([
                'nama' => $request->nama,
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data GWP Cek Pemohon berhasil disimpan',
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
            $data = GwpCekPemohonLs::findOrFail($id);
            $data->update(['nama' => $request->nama]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data GWP Cek Pemohon berhasil diperbarui',
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
            $data = GwpCekPemohonLs::findOrFail($id);
            $data->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data GWP Cek Pemohon berhasil dihapus',
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
