<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbMonth2Microscopy\Http\Controllers\SitbMonth2MicroscopyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-month2-microscopies', SitbMonth2MicroscopyController::class);
});