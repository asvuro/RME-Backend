<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbHivTestResult\Http\Controllers\SitbHivTestResultController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-hiv-test-results', SitbHivTestResultController::class)->only(['index', 'show']);

    Route::apiResource('sitb-hiv-test-results', SitbHivTestResultController::class)->only(['store', 'update', 'destroy']);
});
