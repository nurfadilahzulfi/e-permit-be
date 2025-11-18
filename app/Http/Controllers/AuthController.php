<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkPermit; // Model ini sudah benar
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

    // ======================================================
    // FUNGSI DASHBOARD YANG DIPERBAIKI DAN DILENGKAPI
    // ======================================================
    public function dashboard()
    {
        // 1. Ambil data statistik (Ini sudah benar)
        $stats = [
            'total_users'        => User::count(),
            'pending_checklists' => WorkPermit::where('status', 1)->count(),           // Status 1
            'pending_approvals'  => WorkPermit::where('status', 2)->count(),           // Status 2
            'active_permits'     => WorkPermit::where('status', 3)->count(),           // Status 3
            'pending_closures'   => WorkPermit::whereIn('status', [5, 6, 7])->count(), // Status 5,6,7

            // Key tambahan untuk mencocokkan view Anda
            'pending_permits'    => WorkPermit::whereIn('status', [1, 2, 10])->count(),
            'approved_permits'   => WorkPermit::where('status', 3)->count(),
            'rejected_permits'   => WorkPermit::where('status', 4)->count(),
        ];

        // ==========================================================
        // !!! INI BAGIAN YANG HILANG (PENYEBAB ERROR ANDA) !!!
        // ==========================================================

        // 2. [BARU] Ambil data untuk tabel "Permohonan Terbaru"
        // [PERBAIKAN] Kita panggil relasi yang benar ('pemohon') BUKAN ('user')
        $recent_permits = WorkPermit::with('pemohon')
            ->latest() // Diurutkan dari yang terbaru
            ->take(5)  // Ambil 5 data
            ->get();

        // 3. [PERBAIKAN] Kirim KEDUA variabel ($stats DAN $recent_permits) ke view
        return view('dashboard.index', compact('stats', 'recent_permits'));
    }
}
