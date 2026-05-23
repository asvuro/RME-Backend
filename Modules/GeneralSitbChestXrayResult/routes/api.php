<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbChestXrayResult\Http\Controllers\SitbChestXrayResultController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-chest-xray-results', SitbChestXrayResultController::class);
});