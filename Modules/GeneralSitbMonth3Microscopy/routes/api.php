<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbMonth3Microscopy\Http\Controllers\SitbMonth3MicroscopyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-month3-microscopies', SitbMonth3MicroscopyController::class)->only(['index', 'show']);

    Route::apiResource('sitb-month3-microscopies', SitbMonth3MicroscopyController::class)->only(['store', 'update', 'destroy']);
});
