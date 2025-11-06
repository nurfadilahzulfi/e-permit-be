<?php
namespace App\Http\Controllers;

use App\Models\PermitGwp;
use App\Models\User;
use Illuminate\Http\Request; // <-- 1. TAMBAHKAN
use Illuminate\Support\Facades\Auth;
// <-- 2. TAMBAHKAN

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
            return redirect()->route('dashboard');
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

    // Dashboard admin
    public function dashboard()
    {
        // 3. LOGIC BARU UNTUK STATISTIK
        $stats = [
            'total_users'      => User::count(),
            // Status 1 = Pending SPV, 2 = Pending HSE
            'pending_permits'  => PermitGwp::whereIn('status', [1, 2])->count(),
            // Status 3 = Approved
            'approved_permits' => PermitGwp::where('status', 3)->count(),
            // Status 4 = Rejected
            'rejected_permits' => PermitGwp::where('status', 4)->count(),
        ];

        // 4. KIRIM STATS KE VIEW
        return view('dashboard.index', compact('stats'));
    }
}
