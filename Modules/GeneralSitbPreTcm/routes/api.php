<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbPreTcm\Http\Controllers\SitbPreTcmController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-pre-tcms', SitbPreTcmController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sitb-pre-tcms', SitbPreTcmController::class)->only(['store', 'update', 'destroy']);
    });
});
