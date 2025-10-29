<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// use Illuminate\Validation\ValidationException; // Tidak perlu di-import, sudah dihandle Laravel

class UserController extends Controller
{
    public function index()
    {
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
            $data                  = User::create($validated);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'User berhasil dibuat.', 'data' => $data], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // Larvel menangani ValidationException secara otomatis, kita fokus pada Exception umum
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

            // TAMPILKAN SEMUA FIELD WAJIB DALAM VALIDASI UPDATE
            $validated = $request->validate([
                'nama'       => 'required|string|max:255',
                'nip'        => 'required|string|max:50',  // Tambah: wajib di-update
                'divisi'     => 'required|string|max:100', // Tambah: wajib di-update
                'jabatan'    => 'required|string|max:100', // Tambah: wajib di-update
                'perusahaan' => 'required|string|max:100', // Tambah: wajib di-update

                // Pengecualian unik email: Abaikan ID user yang sedang di-update
                'email'      => 'required|email|unique:user,email,' . $user->id,

                // Password bersifat opsional (nullable) saat update
                'password'   => 'nullable|min:6',
            ]);

            // Pemrosesan Password: hanya hash jika user mengirim password baru
            if (! empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                // Hapus key 'password' dari array $validated agar tidak menimpa password lama
                // dengan nilai NULL atau string kosong.
                unset($validated['password']);
            }

            $user->update($validated);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'User berhasil diperbarui.', 'data' => $user]);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
