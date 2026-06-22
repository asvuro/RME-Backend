<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordNursingIndicator\Http\Controllers\NursingIndicatorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('nursing-indicators', NursingIndicatorController::class)->only(['index', 'show'])->parameters(['nursing-indicators' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('nursing-indicators', NursingIndicatorController::class)->only(['store', 'update', 'destroy'])->parameters(['nursing-indicators' => 'record']);
    });
});
