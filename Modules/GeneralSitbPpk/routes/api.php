<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbPpk\Http\Controllers\SitbPpkController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-ppks', SitbPpkController::class);
});