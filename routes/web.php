<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GwpAlatLsController;
use App\Http\Controllers\GwpCekController;
use App\Http\Controllers\GwpCekHseLsController;
use App\Http\Controllers\GwpCekPemohonLsController;
use App\Http\Controllers\PermitGwpApprovalController;
use App\Http\Controllers\PermitGwpCompletionController;
use App\Http\Controllers\PermitGwpController;
use App\Http\Controllers\PermitTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - E-Permit
|--------------------------------------------------------------------------
*/

// =======================
// AUTHENTICATION
// =======================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =======================
// AREA YANG BUTUH LOGIN (DENGAN ROLE)
// =======================
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // --- KHUSUS ADMIN ---
    Route::middleware(['role:admin'])->group(function () {
        // Rute View (HTML)
        Route::get('/dashboard/user', [UserController::class, 'view'])->name('dashboard.user');
        Route::get('/dashboard/permit-types', [PermitTypeController::class, 'view'])->name('dashboard.permit-types'); // BARU

        // Rute Data (JSON)
        Route::resource('user', UserController::class);
        Route::resource('permit-types', PermitTypeController::class);
    });

    // --- KHUSUS APPROVER (Supervisor & HSE) ---
    Route::middleware(['role:supervisor,hse,admin'])->group(function () {
                                                                                                                                           // Rute View (HTML)
        Route::get('/dashboard/permit-gwp-approval', [PermitGwpApprovalController::class, 'view'])->name('dashboard.permit-gwp-approval'); // BARU

        // Rute Data (JSON)
        Route::get('/permit-gwp-approval', [PermitGwpApprovalController::class, 'index'])
            ->name('permit-gwp-approval.index'); // Ini tetap untuk JSON/JS
        Route::post('/permit-gwp-approval/approve/{permit_gwp_id}', [PermitGwpApprovalController::class, 'approve'])
            ->name('permit-gwp-approval.approve');
        Route::post('/permit-gwp-approval/reject/{permit_gwp_id}', [PermitGwpApprovalController::class, 'reject'])
            ->name('permit-gwp-approval.reject');
    });

    // --- AREA PEMOHON (Bisa juga diakses Admin) ---
    Route::middleware(['role:pemohon,admin'])->group(function () {

        // Rute View (HTML)
        Route::get('/dashboard/permit-gwp', [PermitGwpController::class, 'view'])
            ->name('dashboard.permit-gwp'); // BARU

        // Rute Data (JSON)
        Route::resource('permit-gwp', PermitGwpController::class);
    });

    // --- HISTORI (Bisa dilihat semua) ---
    Route::get('/permit-gwp-approval/history/{permit_gwp_id}', [PermitGwpApprovalController::class, 'show'])
        ->name('permit-gwp-approval.history');
    Route::delete('/permit-gwp-approval/{id}', [PermitGwpApprovalController::class, 'destroy'])
        ->name('permit-gwp-approval.destroy');

    // =======================
    // GWP CEK (MASTER DATA PERTANYAAN) - Khusus Admin
    // =======================
    Route::middleware(['role:admin'])->group(function () {
                                                                                                                                       // Rute View (HTML)
        Route::get('/dashboard/gwp-cek-pemohon-ls', [GwpCekPemohonLsController::class, 'view'])->name('dashboard.gwp-cek-pemohon-ls'); // BARU
        Route::get('/dashboard/gwp-cek-hse-ls', [GwpCekHseLsController::class, 'view'])->name('dashboard.gwp-cek-hse-ls');             // BARU
        Route::get('/dashboard/gwp-alat-ls', [GwpAlatLsController::class, 'view'])->name('dashboard.gwp-alat-ls');                     // BARU

        // Rute Data (JSON)
        Route::resource('gwp-cek-pemohon-ls', GwpCekPemohonLsController::class);
        Route::resource('gwp-cek-hse-ls', GwpCekHseLsController::class);
        Route::resource('gwp-alat-ls', GwpAlatLsController::class);
    });

    // ===========================================
    // GWP CEK (LEMBAR JAWABAN) - Semua Role
    // ===========================================

    // Rute View (HTML)
    Route::get('/gwp-cek/view/{permit_gwp_id}', [GwpCekController::class, 'viewChecklistPage'])
        ->name('gwp-cek.view'); // BARU

    // Rute Data (JSON)
    Route::get('/gwp-cek/{permit_gwp_id}', [GwpCekController::class, 'index'])
        ->name('gwp-cek.index');
    Route::put('/gwp-cek/{id}', [GwpCekController::class, 'update'])
        ->name('gwp-cek.update');

    // =======================
    // PERMIT GWP COMPLETION
    // =======================
    // TODO: Tambahkan Rute View untuk Completion
    Route::resource('permit-gwp-completion', PermitGwpCompletionController::class)->middleware('auth');
});
