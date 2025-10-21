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
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - E-Permit
|--------------------------------------------------------------------------
|
| Semua route web untuk project E-Permit, termasuk login/admin/dashboard
|
*/

// =======================
// AUTHENTICATION
// =======================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard admin (hanya untuk user login)
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth')->name('dashboard');

// =======================
// USERS CRUD
// =======================
Route::resource('users', UsersController::class)->middleware('auth');

// =======================
// PERMIT TYPES CRUD
// =======================
Route::resource('permit-types', PermitTypeController::class)->middleware('auth');

// =======================
// PERMIT GWP CRUD
// =======================
Route::resource('permit-gwp', PermitGwpController::class)->middleware('auth');
Route::resource('permit-gwp-approval', PermitGwpApprovalController::class)->middleware('auth');
Route::resource('permit-gwp-completion', PermitGwpCompletionController::class)->middleware('auth');

// =======================
// GWP CEK
// =======================
Route::resource('gwp-cek', GwpCekController::class)->middleware('auth');
Route::resource('gwp-cek-pemohon-ls', GwpCekPemohonLsController::class)->middleware('auth');
Route::resource('gwp-cek-hse-ls', GwpCekHseLsController::class)->middleware('auth');
Route::resource('gwp-alat-ls', GwpAlatLsController::class)->middleware('auth');
