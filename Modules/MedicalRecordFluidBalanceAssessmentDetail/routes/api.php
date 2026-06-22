<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFluidBalanceAssessmentDetail\Http\Controllers\FluidBalanceAssessmentDetailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('fluid-balance-assessment-details', FluidBalanceAssessmentDetailController::class)->only(['index', 'show'])->parameters(['fluid-balance-assessment-details' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('fluid-balance-assessment-details', FluidBalanceAssessmentDetailController::class)->only(['store', 'update', 'destroy'])->parameters(['fluid-balance-assessment-details' => 'record']);
    });
});
