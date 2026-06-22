<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordPediatricStatus\Http\Controllers\PediatricStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pediatric-statuses', PediatricStatusController::class)->only(['index', 'show'])->parameters([
        'pediatric-statuses' => 'status',
    ]);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('pediatric-statuses', PediatricStatusController::class)->only(['store', 'update', 'destroy'])->parameters([
        'pediatric-statuses' => 'status',
    ]);
    });
});
