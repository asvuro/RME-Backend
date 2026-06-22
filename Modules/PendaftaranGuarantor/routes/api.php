<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranGuarantor\Http\Controllers\GuarantorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('guarantors', GuarantorController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('guarantors', GuarantorController::class)->only(['store', 'update', 'destroy']);
    });
});
