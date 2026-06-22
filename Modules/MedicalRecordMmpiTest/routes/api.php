<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordMmpiTest\Http\Controllers\MmpiTestController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('mmpi-tests', MmpiTestController::class)->only(['index', 'show'])->parameters([
        'mmpi-tests' => 'test',
    ]);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('mmpi-tests', MmpiTestController::class)->only(['store', 'update', 'destroy'])->parameters([
        'mmpi-tests' => 'test',
    ]);
    });
});
