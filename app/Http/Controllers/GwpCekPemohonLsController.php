<?php
namespace App\Http\Controllers;

use App\Models\GwpCekPemohonLs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GwpCekPemohonLsController extends Controller
{
    public function index()
    {
        $data = GwpCekPemohonLs::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show($id)
    {
        $data = GwpCekPemohonLs::find($id);
        if (! $data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:100|unique:gwp_cek_pemohon_ls,nama',
            ]);

            $data = GwpCekPemohonLs::create($validated);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan.', 'data' => $data], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data      = GwpCekPemohonLs::findOrFail($id);
            $validated = $request->validate([
                'nama' => 'required|string|max:100|unique:gwp_cek_pemohon_ls,nama,' . $id,
            ]);
            $data->update($validated);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.', 'data' => $data]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = GwpCekPemohonLs::findOrFail($id);
            $data->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
