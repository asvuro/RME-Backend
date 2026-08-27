<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbDmTherapy\Http\Controllers\SitbDmTherapyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-dm-therapies', SitbDmTherapyController::class)->only(['index', 'show']);

    Route::apiResource('sitb-dm-therapies', SitbDmTherapyController::class)->only(['store', 'update', 'destroy']);
});
