<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbTreatmentStatus\Http\Controllers\SitbTreatmentStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-treatment-statuses', SitbTreatmentStatusController::class)->only(['index', 'show']);

    Route::apiResource('sitb-treatment-statuses', SitbTreatmentStatusController::class)->only(['store', 'update', 'destroy']);
});
