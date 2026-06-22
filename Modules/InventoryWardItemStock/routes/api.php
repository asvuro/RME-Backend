<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryWardItemStock\Http\Controllers\InventoryWardItemStockController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('inventorywarditemstocks', InventoryWardItemStockController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('inventorywarditemstocks', InventoryWardItemStockController::class)->only(['store', 'update', 'destroy']);
    });
});
