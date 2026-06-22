<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordSickLeaveCertificate\Http\Controllers\SickLeaveCertificateController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sick-leave-certificates', SickLeaveCertificateController::class)->only(['index', 'show'])->parameters([
        'sick-leave-certificates' => 'certificate',
    ]);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sick-leave-certificates', SickLeaveCertificateController::class)->only(['store', 'update', 'destroy'])->parameters([
        'sick-leave-certificates' => 'certificate',
    ]);
    });
});
