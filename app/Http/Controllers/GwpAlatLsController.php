<?php
namespace App\Http\Controllers;

use App\Models\GwpAlatLs;
use Illuminate\Contracts\View\View; // <--- [BARU] DITAMBAHKAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GwpAlatLsController extends Controller
{
    /**
     * [BARU] Menampilkan halaman frontend (UI) untuk Master Checklist Alat.
     * Ini dipanggil oleh rute '/dashboard/gwp-alat-ls'
     */
    public function view(): View
    {
        // Pastikan Anda memiliki file Blade di:
        // resources/views/gwp-alat-ls/index.blade.php
        return view('gwp-alat-ls.index');
    }

    /**
     * [TETAP] Mengembalikan data JSON untuk frontend.
     * Ini dipanggil oleh rute 'GET /gwp-alat-ls' (dari JavaScript/AJAX)
     */
    public function index()
    {
        $data = GwpAlatLs::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show($id)
    {
        $data = GwpAlatLs::find($id);
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
                'nama' => 'required|string|max:100|unique:gwp_alat_ls,nama',
            ]);

            $data = GwpAlatLs::create($validated);
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
            $data = GwpAlatLs::findOrFail($id);

            $validated = $request->validate([
                'nama' => 'required|string|max:100|unique:gwp_alat_ls,nama,' . $id,
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
            $data = GwpAlatLs::findOrFail($id);
            $data->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
