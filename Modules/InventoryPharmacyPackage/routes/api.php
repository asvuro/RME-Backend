<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryPharmacyPackage\Http\Controllers\InventoryPharmacyPackageController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharmacy-packages', InventoryPharmacyPackageController::class)->only(['index', 'show'])->parameters(['pharmacy-packages' => 'pharmacy_package']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('pharmacy-packages', InventoryPharmacyPackageController::class)->only(['store', 'update', 'destroy'])->parameters(['pharmacy-packages' => 'pharmacy_package']);
    });
});
