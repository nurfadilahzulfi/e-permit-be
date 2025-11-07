<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkPermit; // Pastikan model ini sudah benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard'); // Mengarahkan ke route 'dashboard'
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // ======================================================
    // INI ADALAH FUNGSI YANG DIPERBAIKI
    // ======================================================
    public function dashboard()
    {
        // 1. Ambil data
        $total_users = User::count();

        // Asumsi:
        // Status 1 = Pending Checklist
        // Status 2 = Pending Approval
        $pending_permits = WorkPermit::whereIn('status', [1, 2])->count();

        // Status 3 = Approved (Siap Dikerjakan)
        $approved_permits = WorkPermit::where('status', 3)->count();

        // Asumsi: Status 4 = Rejected (Sesuaikan jika status Anda berbeda)
        $rejected_permits = WorkPermit::where('status', 4)->count();

        // 2. Buat array $stats dengan KEY YANG BENAR (sesuai index.blade.php)
        $stats = [
            'total_users'      => $total_users,
            'pending_permits'  => $pending_permits,
            'approved_permits' => $approved_permits,
            'rejected_permits' => $rejected_permits,
        ];

        // 3. Kirim data $stats ke view
        return view('dashboard.index', compact('stats'));
    }
}
