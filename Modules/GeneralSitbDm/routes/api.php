<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbDm\Http\Controllers\SitbDmController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-dms', SitbDmController::class);
});