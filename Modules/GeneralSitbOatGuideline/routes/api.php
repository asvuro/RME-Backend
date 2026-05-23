<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbOatGuideline\Http\Controllers\SitbOatGuidelineController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-oat-guidelines', SitbOatGuidelineController::class);
});