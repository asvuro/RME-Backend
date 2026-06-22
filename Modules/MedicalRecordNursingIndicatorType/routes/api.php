<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordNursingIndicatorType\Http\Controllers\NursingIndicatorTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('nursing-indicator-types', NursingIndicatorTypeController::class)->only(['index', 'show'])->parameters(['nursing-indicator-types' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('nursing-indicator-types', NursingIndicatorTypeController::class)->only(['store', 'update', 'destroy'])->parameters(['nursing-indicator-types' => 'record']);
    });
});
