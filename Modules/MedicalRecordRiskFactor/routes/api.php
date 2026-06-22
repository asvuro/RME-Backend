<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordRiskFactor\Http\Controllers\RiskFactorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('risk-factors', RiskFactorController::class)->only(['index', 'show'])->parameters(['risk-factors' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('risk-factors', RiskFactorController::class)->only(['store', 'update', 'destroy'])->parameters(['risk-factors' => 'record']);
    });
});
