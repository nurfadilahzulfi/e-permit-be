<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules; // <-- Pastikan ini ada
use Illuminate\Validation\ValidationException;
// <-- Pastikan ini ada

class UserController extends Controller
{
    /**
     * Menampilkan semua user (hanya untuk Admin)
     */
    public function index()
    {
        $data = User::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Menampilkan detail satu user (hanya untuk Admin)
     */
    public function show($id)
    {
        $data = User::find($id);
        if (! $data) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * [BARU] Halaman view (jika kamu membutuhkannya)
     */
    public function view()
    {
        // Ini adalah fungsi yang kamu panggil di routes/web.php
        // Pastikan file view-nya ada
        return view('user.index');
    }

    /**
     * [DIPERBAIKI] Menyimpan user baru (dari langkah sebelumnya)
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama'       => 'required|string|max:255',
                'email'      => 'required|string|email|max:255|unique:user', // 'unique:user'
                'password'   => ['required', Rules\Password::min(8)],
                'nip'        => 'required|string|max:100',
                'divisi'     => 'required|string|max:100',
                'jabatan'    => 'required|string|max:100',
                'perusahaan' => 'required|string|max:100',
                'role'       => 'required|string|in:admin,supervisor,hse,pemohon',
            ]);

            // Model User.php kamu (dengan setPasswordAttribute) akan
            // otomatis hash password ini.
            $data = User::create($validated);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'User berhasil dibuat.', 'data' => $data], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * [BARU & PENTING] Update data user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id); // 1. Cari user-nya dulu

        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nama'       => 'required|string|max:255',
                // 2. Validasi email: unik, TAPI abaikan ID user ini sendiri
                'email'      => 'required|string|email|max:255|unique:user,email,' . $id,
                // 3. Password sekarang 'nullable' (opsional)
                'password'   => ['nullable', 'string', Rules\Password::min(8)],
                'nip'        => 'required|string|max:100',
                'divisi'     => 'required|string|max:100',
                'jabatan'    => 'required|string|max:100',
                'perusahaan' => 'required|string|max:100',
                'role'       => 'required|string|in:admin,supervisor,hse,pemohon',
            ]);

            // 4. Logic Password Opsional
            if (empty($validated['password'])) {
                // Jika password di JSON kosong ("") atau null,
                // hapus dari array agar tidak meng-update password
                unset($validated['password']);
            }

            // 5. Update data
            // Model User.php kamu akan otomatis hash password JIKA ada
            $user->update($validated);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'User berhasil diperbarui.', 'data' => $user]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus user
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = User::findOrFail($id);
            $data->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
