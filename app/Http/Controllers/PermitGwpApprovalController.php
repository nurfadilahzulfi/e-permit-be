<?php
namespace App\Http\Controllers;

use App\Models\PermitGwpApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PermitGwpApprovalController extends Controller
{
    public function index()
    {
        $data = PermitGwpApproval::with('permit')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show($id)
    {
        $data = PermitGwpApproval::with('permit')->find($id);
        if (! $data) {
            return response()->json(['success' => false, 'message' => 'Approval tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'permit_gwp_id'  => 'required|integer|exists:permit_gwp,id',
                'user_id'        => 'required|integer',
                'jenis_approval' => 'required|string|max:50',
                'status'         => 'required|integer',
                'catatan'        => 'nullable|string',
            ]);

            $data = PermitGwpApproval::create($validated);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Approval berhasil dibuat.', 'data' => $data], 201);
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
            $data      = PermitGwpApproval::findOrFail($id);
            $validated = $request->validate([
                'permit_gwp_id'  => 'required|integer|exists:permit_gwp,id',
                'user_id'        => 'required|integer',
                'jenis_approval' => 'required|string|max:50',
                'status'         => 'required|integer',
                'catatan'        => 'nullable|string',
            ]);

            $data->update($validated);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Approval berhasil diperbarui.', 'data' => $data]);
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
            $data = PermitGwpApproval::findOrFail($id);
            $data->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Approval berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
