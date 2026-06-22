<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranPatientTransfer\Http\Controllers\PatientTransferController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patienttransfers', PatientTransferController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('patienttransfers', PatientTransferController::class)->only(['store']);
    });
});
