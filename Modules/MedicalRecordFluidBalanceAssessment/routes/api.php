<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFluidBalanceAssessment\Http\Controllers\FluidBalanceAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('fluid-balance-assessments', FluidBalanceAssessmentController::class)->only(['index', 'show'])->parameters(['fluid-balance-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('fluid-balance-assessments', FluidBalanceAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['fluid-balance-assessments' => 'record']);
    });
});
