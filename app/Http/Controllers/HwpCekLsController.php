<?php
namespace App\Http\Controllers;

use App\Models\HwpCekLs; // <-- Ganti jika nama Model berbeda
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HwpCekLsController extends Controller
{
    /**
     * Menampilkan halaman frontend (UI) untuk Master Checklist HWP.
     */
    public function view(): View
    {
        return view('hwp-cek-ls.index'); // resources/views/hwp-cek-ls/index.blade.php
    }

    /**
     * Mengembalikan data JSON untuk frontend.
     */
    public function index()
    {
        $data = HwpCekLs::all();
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
                'nama' => 'required|string|max:255|unique:hwp_cek_ls,nama',
            ]);

            $data = HwpCekLs::create($validated);
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
            $data      = HwpCekLs::findOrFail($id);
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:hwp_cek_ls,nama,' . $id,
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
            $data = HwpCekLs::findOrFail($id);
            $data->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
