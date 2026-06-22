<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbAnatomyClassification\Http\Controllers\SitbAnatomyClassificationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-anatomy-classifications', SitbAnatomyClassificationController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sitb-anatomy-classifications', SitbAnatomyClassificationController::class)->only(['store', 'update', 'destroy']);
    });
});
