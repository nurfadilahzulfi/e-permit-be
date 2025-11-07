<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman/view untuk edit profile
     */
    public function show()
    {
        // Mengirim data user yang sedang login ke view
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Memperbarui data profile (Nama, Email, dll)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:user,email,' . $user->id,
            'nip'        => 'required|string|max:100|unique:user,nip,' . $user->id,
            'divisi'     => 'required|string|max:100',
            'jabatan'    => 'required|string|max:100',
            'perusahaan' => 'required|string|max:100',
        ]);

        // Update data user
        $user->update($validated);

        // Kembali ke halaman profile dengan pesan sukses
        return redirect()->route('profile.show')->with('status', 'Profile berhasil diperbarui!');
    }

    /**
     * Memperbarui password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if (! Hash::check($value, $user->password)) {
                    $fail('Password saat ini salah.');
                }
            }],
            'password'         => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')->with('status', 'Password berhasil diubah!');
    }
}
