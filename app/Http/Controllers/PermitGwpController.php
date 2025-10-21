<?php
namespace App\Http\Controllers;

use App\Models\PermitGwp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PermitGwpController extends Controller
{
    public function index()
    {
        $data = PermitGwp::with(['approvals', 'completions'])->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show($id)
    {
        $data = PermitGwp::with(['approvals', 'completions'])->find($id);
        if (! $data) {
            return response()->json(['success' => false, 'message' => 'Permit tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nomor'                => 'required|string|unique:permit_gwp,nomor',
                'tgl_permohonan'       => 'required|date',
                'shift_kerja'          => 'required|string|max:20',
                'lokasi'               => 'required|string|max:255',
                'deskripsi_pekerjaan'  => 'required|string',
                'peralatan_pekerjaan'  => 'required|string',
                'pemohon_id'           => 'required|integer',
                'pemohon_jenis'        => 'required|in:internal,eksternal',
                'pemilik_lokasi_jenis' => 'required|integer',
                'status'               => 'required|integer',
            ]);

            $permit = PermitGwp::create($validated);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Permit berhasil dibuat.', 'data' => $permit], 201);
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
            $permit = PermitGwp::findOrFail($id);

            $validated = $request->validate([
                'nomor'                => 'required|string|unique:permit_gwp,nomor,' . $id,
                'tgl_permohonan'       => 'required|date',
                'shift_kerja'          => 'required|string|max:20',
                'lokasi'               => 'required|string|max:255',
                'deskripsi_pekerjaan'  => 'required|string',
                'peralatan_pekerjaan'  => 'required|string',
                'pemohon_id'           => 'required|integer',
                'pemohon_jenis'        => 'required|in:internal,eksternal',
                'pemilik_lokasi_jenis' => 'required|integer',
                'status'               => 'required|integer',
            ]);

            $permit->update($validated);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Permit berhasil diperbarui.', 'data' => $permit]);
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
            $permit = PermitGwp::findOrFail($id);
            $permit->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Permit berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
