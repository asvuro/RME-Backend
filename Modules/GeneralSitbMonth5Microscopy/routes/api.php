<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbMonth5Microscopy\Http\Controllers\SitbMonth5MicroscopyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-month5-microscopies', SitbMonth5MicroscopyController::class)->only(['index', 'show']);

    Route::apiResource('sitb-month5-microscopies', SitbMonth5MicroscopyController::class)->only(['store', 'update', 'destroy']);
});
