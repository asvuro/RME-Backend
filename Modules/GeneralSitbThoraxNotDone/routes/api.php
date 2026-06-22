<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbThoraxNotDone\Http\Controllers\SitbThoraxNotDoneController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-thorax-not-dones', SitbThoraxNotDoneController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sitb-thorax-not-dones', SitbThoraxNotDoneController::class)->only(['store', 'update', 'destroy']);
    });
});
