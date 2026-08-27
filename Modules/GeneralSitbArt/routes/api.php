<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbArt\Http\Controllers\SitbArtController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-arts', SitbArtController::class)->only(['index', 'show']);

    Route::apiResource('sitb-arts', SitbArtController::class)->only(['store', 'update', 'destroy']);
});
