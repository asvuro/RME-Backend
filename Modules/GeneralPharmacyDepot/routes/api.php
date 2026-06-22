<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPharmacyDepot\Http\Controllers\PharmacyDepotController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharmacy-depots', PharmacyDepotController::class)->only(['index', 'show'])->parameters(['pharmacy-depots' => 'pharmacy_depot']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('pharmacy-depots', PharmacyDepotController::class)->only(['store', 'update', 'destroy'])->parameters(['pharmacy-depots' => 'pharmacy_depot']);
    });
});
