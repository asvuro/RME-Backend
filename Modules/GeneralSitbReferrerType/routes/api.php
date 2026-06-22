<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbReferrerType\Http\Controllers\SitbReferrerTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-referrer-types', SitbReferrerTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sitb-referrer-types', SitbReferrerTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
