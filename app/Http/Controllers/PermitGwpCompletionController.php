<?php
namespace App\Http\Controllers;

use App\Models\PermitGwpCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermitGwpCompletionController extends Controller
{
    public function index()
    {
        return response()->json(PermitGwpCompletion::all());
    }

    public function show($id)
    {
        $completion = PermitGwpCompletion::find($id);
        return $completion ? response()->json($completion) : response()->json(['message' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $completion = PermitGwpCompletion::create($request->all());
            DB::commit();
            return response()->json($completion, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $completion = PermitGwpCompletion::findOrFail($id);
            $completion->update($request->all());
            DB::commit();
            return response()->json($completion);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $completion = PermitGwpCompletion::findOrFail($id);
            $completion->delete();
            DB::commit();
            return response()->json(['message' => 'Completion deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
