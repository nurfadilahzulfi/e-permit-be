<?php

use App\Http\Controllers\GwpAlatLsController;
use App\Http\Controllers\GwpCekHseLsController;
use App\Http\Controllers\GwpCekPemohonLsController;
use App\Http\Controllers\PermitTypeController;
use Illuminate\Support\Facades\Route;

Route::resource('permit-types', PermitTypeController::class);
Route::resource('gwp-cek-pemohon-ls', GwpCekPemohonLsController::class);
Route::resource('gwp-cek-hse-ls', GwpCekHseLsController::class);
Route::resource('gwp-alat-ls', GwpAlatLsController::class);
