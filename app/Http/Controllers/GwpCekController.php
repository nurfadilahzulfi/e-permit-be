<?php
namespace App\Http\Controllers;

use App\Models\GwpCek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GwpCekController extends Controller
{
    public function index()
    {
        return response()->json(GwpCek::all());
    }

    public function show($id)
    {
        $cek = GwpCek::find($id);
        return $cek ? response()->json($cek) : response()->json(['message' => 'Not found'], 404);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $cek = GwpCek::create($request->all());
            DB::commit();
            return response()->json($cek, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $cek = GwpCek::findOrFail($id);
            $cek->update($request->all());
            DB::commit();
            return response()->json($cek);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $cek = GwpCek::findOrFail($id);
            $cek->delete();
            DB::commit();
            return response()->json(['message' => 'Cek deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
