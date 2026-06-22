<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralAdministration\Http\Controllers\AdministrationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('administrations', AdministrationController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('administrations', AdministrationController::class)->only(['store', 'update', 'destroy']);
    });
});
