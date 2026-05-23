<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbPreMicroscopy\Http\Controllers\SitbPreMicroscopyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-pre-microscopies', SitbPreMicroscopyController::class);
});