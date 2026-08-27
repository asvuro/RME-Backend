<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbHivStatusClassification\Http\Controllers\SitbHivStatusClassificationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-hiv-status-classifications', SitbHivStatusClassificationController::class)->only(['index', 'show']);

    Route::apiResource('sitb-hiv-status-classifications', SitbHivStatusClassificationController::class)->only(['store', 'update', 'destroy']);
});
