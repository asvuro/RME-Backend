<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbTreatmentOutcome\Http\Controllers\SitbTreatmentOutcomeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-treatment-outcomes', SitbTreatmentOutcomeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sitb-treatment-outcomes', SitbTreatmentOutcomeController::class)->only(['store', 'update', 'destroy']);
    });
});
