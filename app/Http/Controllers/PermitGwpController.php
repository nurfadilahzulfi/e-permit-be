<?php
namespace App\Http\Controllers;

use App\Models\PermitGwp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermitGwpController extends Controller
{
    public function index()
    {
        return response()->json(PermitGwp::with(['pemohon', 'approvals', 'completions'])->get());
    }

    public function show($id)
    {
        $data = PermitGwp::with(['pemohon', 'approvals', 'completions'])->find($id);
        return $data ? response()->json($data) : response()->json(['message' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $permit = PermitGwp::create($request->all());
            DB::commit();
            return response()->json($permit, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $permit = PermitGwp::findOrFail($id);
            $permit->update($request->all());
            DB::commit();
            return response()->json($permit);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $permit = PermitGwp::findOrFail($id);
            $permit->delete();
            DB::commit();
            return response()->json(['message' => 'Permit deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
