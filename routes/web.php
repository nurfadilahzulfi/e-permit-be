<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CseCekController;
use App\Http\Controllers\EwpCekController;
use App\Http\Controllers\EwpCekLsController;
use App\Http\Controllers\GwpAlatLsController;
use App\Http\Controllers\GwpCekController;
use App\Http\Controllers\GwpCekHseLsController;
use App\Http\Controllers\GwpCekPemohonLsController;
use App\Http\Controllers\HwpCekController;
use App\Http\Controllers\HwpCekLsController;
use App\Http\Controllers\LpCekController;
use App\Http\Controllers\LpCekLsController;

// [BENAR] Import controller
use App\Http\Controllers\PermitTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkPermitApprovalController;
use App\Http\Controllers\WorkPermitCompletionController;
use App\Http\Controllers\WorkPermitController;
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

        // Master GWP
        Route::get('/dashboard/gwp-cek-pemohon-ls', [GwpCekPemohonLsController::class, 'view'])->name('dashboard.gwp-cek-pemohon-ls');
        Route::get('/dashboard/gwp-cek-hse-ls', [GwpCekHseLsController::class, 'view'])->name('dashboard.gwp-cek-hse-ls');
        Route::get('/dashboard/gwp-alat-ls', [GwpAlatLsController::class, 'view'])->name('dashboard.gwp-alat-ls');
        Route::resource('gwp-cek-pemohon-ls', GwpCekPemohonLsController::class);
        Route::resource('gwp-cek-hse-ls', GwpCekHseLsController::class);
        Route::resource('gwp-alat-ls', GwpAlatLsController::class);

        // --- [BARU] Master Data Checklist HWP, EWP, LP ---
        Route::get('/dashboard/hwp-cek-ls', [HwpCekLsController::class, 'view'])->name('dashboard.hwp-cek-ls');
        Route::resource('hwp-cek-ls', HwpCekLsController::class);

        Route::get('/dashboard/ewp-cek-ls', [EwpCekLsController::class, 'view'])->name('dashboard.ewp-cek-ls');
        Route::resource('ewp-cek-ls', EwpCekLsController::class);

        Route::get('/dashboard/lp-cek-ls', [LpCekLsController::class, 'view'])->name('dashboard.lp-cek-ls');
        Route::resource('lp-cek-ls', LpCekLsController::class);
    });

    // Rute User & Profile (non-admin)
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // ===========================================
    // ALUR KERJA IZIN BARU (WORK PERMIT)
    // ===========================================

    // --- 1. Peninjauan Izin (HSE / Admin) ---
    Route::get('/dashboard/work-permit-review', [WorkPermitController::class, 'viewHseReview'])
        ->name('dashboard.work-permit-review')
        ->middleware(['role:hse,admin']);

    Route::get('/work-permit/hse-review-list', [WorkPermitController::class, 'indexHseReview'])
        ->name('work-permit.hse-review-list')
        ->middleware(['role:hse,admin']);

    Route::post('/work-permit/{id}/review-assign', [WorkPermitController::class, 'reviewAndAssign'])
        ->name('work-permit.review-assign')
        ->middleware(['role:hse,admin']);

    // --- 2. Halaman Pemohon (List Tugas) ---
    Route::get('/dashboard/my-permits', [WorkPermitController::class, 'viewMyPermits'])
        ->name('dashboard.my-permits')
        ->middleware(['role:pemohon,supervisor,hse,admin']); // [PERBAIKAN] Izinkan semua role

    // [PERBAIKAN] Middleware diperluas
    Route::get('/work-permit-list', [WorkPermitController::class, 'index'])
        ->name('work-permit.index')
        ->middleware(['role:pemohon,supervisor,hse,admin']);

    Route::post('/work-permit/request-job', [WorkPermitController::class, 'requestJob'])
        ->name('work-permit.request-job')
        ->middleware(['role:pemohon']);

    Route::post('/work-permit/{id}/submit-approval', [WorkPermitController::class, 'submitForApproval'])
        ->name('work-permit.submit-approval')
        ->middleware(['role:pemohon']);

    // --- 3. Halaman Persetujuan (HSE / Supervisor) ---
    Route::get('/dashboard/work-permit-approval', [WorkPermitApprovalController::class, 'view'])
        ->name('dashboard.work-permit-approval')
        ->middleware(['role:supervisor,hse,admin']);
    Route::get('/work-permit-approval-list', [WorkPermitApprovalController::class, 'index'])
        ->name('work-permit-approval.index')
        ->middleware(['role:supervisor,hse,admin']);

    // [PERBAIKAN] Parameter {id} adalah ID dari 'work_permit_approval'
    Route::post('/work-permit-approval/{id}/approve', [WorkPermitApprovalController::class, 'approve'])
        ->name('work-permit-approval.approve')
        ->middleware(['role:supervisor,hse,admin']);
    Route::post('/work-permit-approval/{id}/reject', [WorkPermitApprovalController::class, 'reject'])
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
    // CHECKLIST (CSE)
    // ===========================================
    Route::get('/cse-cek/view/{permit_cse_id}', [CseCekController::class, 'viewChecklistPage'])
        ->name('cse-cek.view');
    Route::get('/cse-cek/{permit_cse_id}', [CseCekController::class, 'index'])
        ->name('cse-cek.index');
    Route::put('/cse-cek/{id}', [CseCekController::class, 'update'])
        ->name('cse-cek.update');

    // ===========================================
    // CHECKLIST (HWP) [BARU]
    // ===========================================
    Route::get('/hwp-cek/view/{permit_hwp_id}', [HwpCekController::class, 'viewChecklistPage'])
        ->name('hwp-cek.view');
    Route::get('/hwp-cek/{permit_hwp_id}', [HwpCekController::class, 'index'])
        ->name('hwp-cek.index');
    Route::put('/hwp-cek/{id}', [HwpCekController::class, 'update'])
        ->name('hwp-cek.update');

    // ===========================================
    // CHECKLIST (EWP) [BARU]
    // ===========================================
    Route::get('/ewp-cek/view/{permit_ewp_id}', [EwpCekController::class, 'viewChecklistPage'])
        ->name('ewp-cek.view');
    Route::get('/ewp-cek/{permit_ewp_id}', [EwpCekController::class, 'index'])
        ->name('ewp-cek.index');
    Route::put('/ewp-cek/{id}', [EwpCekController::class, 'update'])
        ->name('ewp-cek.update');

    // ===========================================
    // CHECKLIST (LP) [BARU]
    // ===========================================
    Route::get('/lp-cek/view/{permit_lp_id}', [LpCekController::class, 'viewChecklistPage'])
        ->name('lp-cek.view');
    Route::get('/lp-cek/{permit_lp_id}', [LpCekController::class, 'index'])
        ->name('lp-cek.index');
    Route::put('/lp-cek/{id}', [LpCekController::class, 'update'])
        ->name('lp-cek.update');

    // ===========================================
    // ALUR PENUTUPAN IZIN (COMPLETION)
    // ===========================================
    Route::post('/work-permit/{id}/start-completion', [WorkPermitCompletionController::class, 'startCompletion'])
        ->name('work-permit.start-completion')
        ->middleware(['role:pemohon']);

    Route::get('/dashboard/work-permit-completion', [WorkPermitCompletionController::class, 'view'])
        ->name('dashboard.work-permit-completion')
        ->middleware(['role:hse,supervisor,pemohon']);

    Route::get('/work-permit-completion-list', [WorkPermitCompletionController::class, 'index'])
        ->name('work-permit-completion.index')
        ->middleware(['role:hse,supervisor,pemohon']);

    Route::post('/work-permit-completion/sign', [WorkPermitCompletionController::class, 'signCompletion'])
        ->name('work-permit-completion.sign')
        ->middleware(['role:hse,supervisor,pemohon']);
});
