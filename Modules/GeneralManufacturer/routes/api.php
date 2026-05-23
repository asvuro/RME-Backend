<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralManufacturer\Http\Controllers\ManufacturerController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('manufacturers', ManufacturerController::class);
});