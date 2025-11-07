<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CseCekController;
use App\Http\Controllers\GwpAlatLsController;
use App\Http\Controllers\GwpCekController;
use App\Http\Controllers\GwpCekHseLsController;
use App\Http\Controllers\GwpCekPemohonLsController;
use App\Http\Controllers\PermitTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkPermitApprovalController;
use App\Http\Controllers\WorkPermitCompletionController;
use App\Http\Controllers\WorkPermitController; // <-- [BARU] DITAMBAHKAN
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - E-Permit (Struktur Baru)
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

    // --- MASTER DATA (Admin) ---
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dashboard/user', [UserController::class, 'view'])->name('dashboard.user');
        Route::resource('user', UserController::class)->except(['index', 'show']);
        Route::get('/dashboard/permit-types', [PermitTypeController::class, 'view'])->name('dashboard.permit-types');
        Route::resource('permit-types', PermitTypeController::class);
        Route::get('/dashboard/gwp-cek-pemohon-ls', [GwpCekPemohonLsController::class, 'view'])->name('dashboard.gwp-cek-pemohon-ls');
        Route::get('/dashboard/gwp-cek-hse-ls', [GwpCekHseLsController::class, 'view'])->name('dashboard.gwp-cek-hse-ls');
        Route::get('/dashboard/gwp-alat-ls', [GwpAlatLsController::class, 'view'])->name('dashboard.gwp-alat-ls');
        Route::resource('gwp-cek-pemohon-ls', GwpCekPemohonLsController::class);
        Route::resource('gwp-cek-hse-ls', GwpCekHseLsController::class);
        Route::resource('gwp-alat-ls', GwpAlatLsController::class);
    });
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // ===========================================
    // ALUR KERJA IZIN BARU (WORK PERMIT)
    // ===========================================

    // --- 1. Inisiasi Izin (HSE / Admin) ---
    Route::get('/dashboard/work-permit', [WorkPermitController::class, 'view'])
        ->name('dashboard.work-permit')
        ->middleware(['role:hse,admin']);
    Route::post('/work-permit', [WorkPermitController::class, 'store'])
        ->name('work-permit.store')
        ->middleware(['role:hse,admin']);

    // --- 2. Halaman Pemohon (List Tugas) ---
    Route::get('/dashboard/my-permits', [WorkPermitController::class, 'viewMyPermits'])
        ->name('dashboard.my-permits')
        ->middleware(['role:pemohon']);
    Route::get('/work-permit-list', [WorkPermitController::class, 'index'])
        ->name('work-permit.index')
        ->middleware(['role:pemohon']);

    // --- 3. Halaman Persetujuan (HSE / Supervisor) ---
    Route::get('/dashboard/work-permit-approval', [WorkPermitApprovalController::class, 'view'])
        ->name('dashboard.work-permit-approval')
        ->middleware(['role:supervisor,hse,admin']);
    Route::get('/work-permit-approval-list', [WorkPermitApprovalController::class, 'index'])
        ->name('work-permit-approval.index')
        ->middleware(['role:supervisor,hse,admin']);
    Route::post('/work-permit-approval/approve', [WorkPermitApprovalController::class, 'approve'])
        ->name('work-permit-approval.approve')
        ->middleware(['role:supervisor,hse,admin']);
    Route::post('/work-permit-approval/reject', [WorkPermitApprovalController::class, 'reject'])
        ->name('work-permit-approval.reject')
        ->middleware(['role:supervisor,hse,admin']);

    // ===========================================
    // CHECKLIST (GWP)
    // ===========================================
    Route::get('/gwp-cek/view/{permit_gwp_id}', [GwpCekController::class, 'viewChecklistPage'])
        ->name('gwp-cek.view');
    Route::get('/gwp-cek/{permit_gwp_id}', [GwpCekController::class, 'index'])
        ->name('gwp-cek.index');
    Route::put('/gwp-cek/{id}', [GwpCekController::class, 'update'])
        ->name('gwp-cek.update');

    // ===========================================
    // [BARU] CHECKLIST (CSE)
    // ===========================================
    Route::get('/cse-cek/view/{permit_cse_id}', [CseCekController::class, 'viewChecklistPage'])
        ->name('cse-cek.view');
    Route::get('/cse-cek/{permit_cse_id}', [CseCekController::class, 'index'])
        ->name('cse-cek.index');
    Route::put('/cse-cek/{id}', [CseCekController::class, 'update'])
        ->name('cse-cek.update');

    // =================================================================
    // !!! INI "JEMBATAN" BARU KITA !!!
    // =================================================================
    Route::post('/work-permit/{id}/submit-approval', [WorkPermitController::class, 'submitForApproval'])
        ->name('work-permit.submit-approval')
        ->middleware(['role:pemohon']);

    // ===========================================
    // ALUR PENUTUPAN IZIN (COMPLETION)
    // ===========================================

    // [BARU] Rute untuk Tombol "Pekerjaan Selesai" (Langkah 7)
    Route::post('/work-permit/{id}/start-completion', [WorkPermitCompletionController::class, 'startCompletion'])
        ->name('work-permit.start-completion')
        ->middleware(['role:pemohon']);

    // [BARU] Halaman (View) untuk list "Pengesahan Selesai"
    // (Dilihat oleh HSE, Supervisor, Pemohon)
    Route::get('/dashboard/work-permit-completion', [WorkPermitCompletionController::class, 'view'])
        ->name('dashboard.work-permit-completion')
        ->middleware(['role:hse,supervisor,pemohon']);

    // [BARU] Rute JSON untuk mengambil data list "Pengesahan Selesai"
    Route::get('/work-permit-completion-list', [WorkPermitCompletionController::class, 'index'])
        ->name('work-permit-completion.index')
        ->middleware(['role:hse,supervisor,pemohon']);

    // [BARU] Rute Aksi untuk "Tanda Tangan"
    Route::post('/work-permit-completion/sign', [WorkPermitCompletionController::class, 'signCompletion'])
        ->name('work-permit-completion.sign')
        ->middleware(['role:hse,supervisor,pemohon']);
});
