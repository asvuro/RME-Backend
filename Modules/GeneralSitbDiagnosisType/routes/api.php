<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbDiagnosisType\Http\Controllers\SitbDiagnosisTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-diagnosis-types', SitbDiagnosisTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sitb-diagnosis-types', SitbDiagnosisTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
