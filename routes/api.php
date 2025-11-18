<?php

use App\Http\Controllers\CseCekController;
use App\Http\Controllers\EwpCekController;

// ===== IMPORT SEMUA CONTROLLER =====

// Auth & User
use App\Http\Controllers\EwpCekLsController;
use App\Http\Controllers\GwpAlatLsController; // (Opsional jika API perlu ini)

// Alur Kerja Utama
use App\Http\Controllers\GwpCekController;
use App\Http\Controllers\GwpCekHseLsController;
use App\Http\Controllers\GwpCekPemohonLsController;

// Master Data (Admin)
use App\Http\Controllers\HwpCekController;
use App\Http\Controllers\HwpCekLsController;
use App\Http\Controllers\LpCekController;
use App\Http\Controllers\LpCekLsController;
use App\Http\Controllers\PermitTypeController; // [BARU]
use App\Http\Controllers\ProfileController;    // [BARU]
use App\Http\Controllers\UserController;       // [BARU]

// Pengisian Checklist (User)
use App\Http\Controllers\WorkPermitApprovalController;
use App\Http\Controllers\WorkPermitCompletionController;
use App\Http\Controllers\WorkPermitController; // [BARU]
use Illuminate\Http\Request;                   // [BARU]
use Illuminate\Support\Facades\Route;
// [BARU]
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rute default Sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ==========================================================
// SEMUA RUTE API LAINNYA HARUS DIAUTENTIKASI DENGAN SANCTUM
// ==========================================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Master User & Tipe Permit ---
    Route::apiResource('user', UserController::class);
    Route::apiResource('permit-types', PermitTypeController::class);

    // --- Master Checklist (GWP) ---
    Route::apiResource('master/gwp-pemohon-ls', GwpCekPemohonLsController::class);
    Route::apiResource('master/gwp-hse-ls', GwpCekHseLsController::class);
    Route::apiResource('master/gwp-alat-ls', GwpAlatLsController::class);

    // --- [BARU] Master Checklist (HWP, EWP, LP) ---
    Route::apiResource('master/hwp-ls', HwpCekLsController::class);
    Route::apiResource('master/ewp-ls', EwpCekLsController::class);
    Route::apiResource('master/lp-ls', LpCekLsController::class);

    // ===========================================
    // ALUR KERJA IZIN UTAMA (WORK PERMIT)
    // ===========================================
    Route::prefix('work-permits')->group(function () {
        // [BARU] (HSE) Mengambil daftar izin (Status 10) untuk di-review
        Route::get('/review-list', [WorkPermitController::class, 'indexHseReview']);

        // [BARU] (HSE) Menyimpan hasil review (Langkah 3)
        Route::post('/{id}/review-assign', [WorkPermitController::class, 'reviewAndAssign']);

        // [BARU] (PEMOHON/SPV/HSE) Mengambil daftar tugas (Status 1, 2, 3, dll)
        Route::get('/', [WorkPermitController::class, 'index']);

        // [BARU] (PEMOHON) Mengajukan izin baru (Langkah 2)
        Route::post('/request-job', [WorkPermitController::class, 'requestJob']);

        // [BARU] (PEMOHON) Mengirim checklist untuk approval (Langkah 4)
        Route::post('/{id}/submit-approval', [WorkPermitController::class, 'submitForApproval']);
    });

    // ===========================================
    // ALUR PERSETUJUAN (APPROVAL) [BARU]
    // ===========================================
    Route::prefix('work-permit-approval')->group(function () {
        // (HSE/SPV) Mengambil daftar izin yang perlu di-approve
        Route::get('/', [WorkPermitApprovalController::class, 'index']);

        // (HSE/SPV) Menyetujui izin
        // {id} adalah ID dari 'work_permit_approval'
        Route::post('/{id}/approve', [WorkPermitApprovalController::class, 'approve']);

        // (HSE/SPV) Menolak izin
        // {id} adalah ID dari 'work_permit_approval'
        Route::post('/{id}/reject', [WorkPermitApprovalController::class, 'reject']);
    });

    // ===========================================
    // ALUR PENUTUPAN (COMPLETION) [BARU]
    // ===========================================
    Route::prefix('work-permit-completion')->group(function () {
        // (PEMOHON) Memulai alur penutupan (Langkah 7)
        // {id} adalah ID dari 'work_permit'
        Route::post('/{id}/start', [WorkPermitCompletionController::class, 'startCompletion']);

        // (PEMOHON/HSE/SPV) Mengambil daftar tugas penutupan
        Route::get('/', [WorkPermitCompletionController::class, 'index']);

        // (PEMOHON/HSE/SPV) Menandatangani penutupan (Langkah 8, 9, 10)
        Route::post('/sign', [WorkPermitCompletionController::class, 'signCompletion']);
    });

    // ===========================================
    // PENGISIAN CHECKLIST (PEMOHON/HSE)
    // ===========================================

    // --- Checklist GWP ---
    Route::prefix('gwp-cek')->group(function () {
        // Mengambil (GET) semua item checklist untuk 1 permit GWP
        Route::get('/{permit_gwp_id}', [GwpCekController::class, 'index']);
        // Mengupdate (PUT) 1 item checklist
        Route::put('/{id}', [GwpCekController::class, 'update']);
    });

    // --- Checklist CSE ---
    Route::prefix('cse-cek')->group(function () {
        Route::get('/{permit_cse_id}', [CseCekController::class, 'index']);
        Route::put('/{id}', [CseCekController::class, 'update']);
    });

    // --- Checklist HWP [BARU] ---
    Route::prefix('hwp-cek')->group(function () {
        Route::get('/{permit_hwp_id}', [HwpCekController::class, 'index']);
        Route::put('/{id}', [HwpCekController::class, 'update']);
    });

    // --- Checklist EWP [BARU] ---
    Route::prefix('ewp-cek')->group(function () {
        Route::get('/{permit_ewp_id}', [EwpCekController::class, 'index']);
        Route::put('/{id}', [EwpCekController::class, 'update']);
    });

    // --- Checklist LP [BARU] ---
    Route::prefix('lp-cek')->group(function () {
        Route::get('/{permit_lp_id}', [LpCekController::class, 'index']);
        Route::put('/{id}', [LpCekController::class, 'update']);
    });

});
