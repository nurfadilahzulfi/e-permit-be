<?php
namespace App\Http\Controllers;

use App\Models\PermitGwpApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermitGwpApprovalController extends Controller
{
    public function index()
    {
        return response()->json(PermitGwpApproval::all());
    }

    public function show($id)
    {
        $data = PermitGwpApproval::find($id);
        return $data ? response()->json($data) : response()->json(['message' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $approval = PermitGwpApproval::create($request->all());
            DB::commit();
            return response()->json($approval, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $approval = PermitGwpApproval::findOrFail($id);
            $approval->update($request->all());
            DB::commit();
            return response()->json($approval);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $approval = PermitGwpApproval::findOrFail($id);
            $approval->delete();
            DB::commit();
            return response()->json(['message' => 'Approval deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
