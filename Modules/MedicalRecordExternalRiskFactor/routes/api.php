<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordExternalRiskFactor\Http\Controllers\ExternalRiskFactorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('external-risk-factors', ExternalRiskFactorController::class)->only(['index', 'show'])->parameters(['external-risk-factors' => 'record']);

    Route::apiResource('external-risk-factors', ExternalRiskFactorController::class)->only(['store', 'update', 'destroy'])->parameters(['external-risk-factors' => 'record']);
});
