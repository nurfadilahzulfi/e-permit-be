<?php
namespace App\Http\Controllers;

use App\Models\LpCekLs; // <-- Ganti jika nama Model berbeda
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LpCekLsController extends Controller
{
    /**
     * Menampilkan halaman frontend (UI) untuk Master Checklist LP.
     */
    public function view(): View
    {
        return view('lp-cek-ls.index'); // resources/views/lp-cek-ls/index.blade.php
    }

    /**
     * Mengembalikan data JSON untuk frontend.
     */
    public function index()
    {
        $data = LpCekLs::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Menyimpan data master checklist baru.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:lp_cek_ls,nama',
            ]);

            $data = LpCekLs::create($validated);
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

    /**
     * Update data master checklist.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data      = LpCekLs::findOrFail($id);
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:lp_cek_ls,nama,' . $id,
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

    /**
     * Hapus data master checklist.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = LpCekLs::findOrFail($id);
            $data->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
