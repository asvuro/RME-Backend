<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralManufacturer\Http\Controllers\ManufacturerController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('manufacturers', ManufacturerController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('manufacturers', ManufacturerController::class)->only(['store', 'update', 'destroy']);
    });
});
