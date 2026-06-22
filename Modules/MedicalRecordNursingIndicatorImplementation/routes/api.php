<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordNursingIndicatorImplementation\Http\Controllers\NursingIndicatorImplementationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('nursing-indicator-implementations', NursingIndicatorImplementationController::class)->only(['index', 'show'])->parameters(['nursing-indicator-implementations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('nursing-indicator-implementations', NursingIndicatorImplementationController::class)->only(['store', 'destroy'])->parameters(['nursing-indicator-implementations' => 'record']);
    });
});
