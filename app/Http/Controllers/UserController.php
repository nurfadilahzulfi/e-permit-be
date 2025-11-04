<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (! request()->wantsJson()) {
            $data = User::orderBy('id', 'desc')->get();
            return view('user.index', compact('data'));
        }

        $data = User::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show($id)
    {
        $data = User::find($id);
        if (! $data) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama'       => 'required|string|max:255',
                'nip'        => 'required|string|max:50',
                'divisi'     => 'required|string|max:100',
                'jabatan'    => 'required|string|max:100',
                'perusahaan' => 'required|string|max:100',
                'email'      => 'required|email|unique:user,email',
                'password'   => 'required|min:6',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $user                  = User::create($validated);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'User berhasil dibuat.', 'data' => $user], 201);
            }

            return redirect()->route('user.index')->with('success', 'User berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'nama'       => 'required|string|max:255',
                'nip'        => 'required|string|max:50',
                'divisi'     => 'required|string|max:100',
                'jabatan'    => 'required|string|max:100',
                'perusahaan' => 'required|string|max:100',
                'email'      => 'required|email|unique:user,email,' . $user->id,
                'password'   => 'nullable|min:6',
            ]);

            if (! empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'User berhasil diperbarui.', 'data' => $user]);
            }

            return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->delete();
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
            }

            return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
