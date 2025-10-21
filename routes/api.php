<?php

use App\Http\Controllers\GwpAlatLsController;
use App\Http\Controllers\GwpCekController;

// Import semua controller

use App\Http\Controllers\GwpCekHseLsController;
use App\Http\Controllers\GwpCekPemohonLsController;
use App\Http\Controllers\PermitGwpApprovalController;
use App\Http\Controllers\PermitGwpCompletionController;
use App\Http\Controllers\PermitTypeController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Autentikasi user via Sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('users')->group(function () {
    Route::get('/', [UsersController::class, 'index']);
    Route::get('/{id}', [UsersController::class, 'show']);
    Route::post('/', [UsersController::class, 'store']);
    Route::put('/{id}', [UsersController::class, 'update']);
    Route::delete('/{id}', [UsersController::class, 'destroy']);
});

Route::prefix('permit-types')->group(function () {
    Route::get('/', [PermitTypeController::class, 'index']);
    Route::get('/{id}', [PermitTypeController::class, 'show']);
    Route::post('/', [PermitTypeController::class, 'store']);
    Route::put('/{id}', [PermitTypeController::class, 'update']);
    Route::delete('/{id}', [PermitTypeController::class, 'destroy']);
});

Route::prefix('gwp-cek-pemohon-ls')->group(function () {
    Route::get('/', [GwpCekPemohonLsController::class, 'index']);
    Route::get('/{id}', [GwpCekPemohonLsController::class, 'show']);
    Route::post('/', [GwpCekPemohonLsController::class, 'store']);
    Route::put('/{id}', [GwpCekPemohonLsController::class, 'update']);
    Route::delete('/{id}', [GwpCekPemohonLsController::class, 'destroy']);
});

Route::prefix('gwp-cek-hse-ls')->group(function () {
    Route::get('/', [GwpCekHseLsController::class, 'index']);
    Route::get('/{id}', [GwpCekHseLsController::class, 'show']);
    Route::post('/', [GwpCekHseLsController::class, 'store']);
    Route::put('/{id}', [GwpCekHseLsController::class, 'update']);
    Route::delete('/{id}', [GwpCekHseLsController::class, 'destroy']);
});

Route::prefix('gwp-alat-ls')->group(function () {
    Route::get('/', [GwpAlatLsController::class, 'index']);
    Route::get('/{id}', [GwpAlatLsController::class, 'show']);
    Route::post('/', [GwpAlatLsController::class, 'store']);
    Route::put('/{id}', [GwpAlatLsController::class, 'update']);
    Route::delete('/{id}', [GwpAlatLsController::class, 'destroy']);
});

Route::prefix('gwp-cek')->group(function () {
    Route::get('/', [GwpCekController::class, 'index']);
    Route::get('/{id}', [GwpCekController::class, 'show']);
    Route::post('/', [GwpCekController::class, 'store']);
    Route::put('/{id}', [GwpCekController::class, 'update']);
    Route::delete('/{id}', [GwpCekController::class, 'destroy']);
});

Route::prefix('permit-gwp-approval')->group(function () {
    Route::get('/', [PermitGwpApprovalController::class, 'index']);
    Route::get('/{id}', [PermitGwpApprovalController::class, 'show']);
    Route::post('/', [PermitGwpApprovalController::class, 'store']);
    Route::put('/{id}', [PermitGwpApprovalController::class, 'update']);
    Route::delete('/{id}', [PermitGwpApprovalController::class, 'destroy']);
});

Route::prefix('permit-gwp-completion')->group(function () {
    Route::get('/', [PermitGwpCompletionController::class, 'index']);
    Route::get('/{id}', [PermitGwpCompletionController::class, 'show']);
    Route::post('/', [PermitGwpCompletionController::class, 'store']);
    Route::put('/{id}', [PermitGwpCompletionController::class, 'update']);
    Route::delete('/{id}', [PermitGwpCompletionController::class, 'destroy']);
});
