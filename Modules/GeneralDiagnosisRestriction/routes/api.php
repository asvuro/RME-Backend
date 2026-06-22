<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralDiagnosisRestriction\Http\Controllers\DiagnosisRestrictionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('diagnosis-restrictions', DiagnosisRestrictionController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('diagnosis-restrictions', DiagnosisRestrictionController::class)->only(['store', 'update', 'destroy']);
    });
});
