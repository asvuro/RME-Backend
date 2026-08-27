<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbTreatmentHistoryClassification\Http\Controllers\SitbTreatmentHistoryClassificationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-treatment-history-classifications', SitbTreatmentHistoryClassificationController::class)->only(['index', 'show'])->parameters(['sitb-treatment-history-classifications' => 'record']);

    Route::apiResource('sitb-treatment-history-classifications', SitbTreatmentHistoryClassificationController::class)->only(['store', 'update', 'destroy'])->parameters(['sitb-treatment-history-classifications' => 'record']);
});
