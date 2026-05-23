<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSitbPreCulture\Http\Controllers\SitbPreCultureController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sitb-pre-cultures', SitbPreCultureController::class);
});