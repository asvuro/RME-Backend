<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryItemClassification\Http\Controllers\InventoryItemClassificationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('inventoryitemclassifications', InventoryItemClassificationController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('inventoryitemclassifications', InventoryItemClassificationController::class)->only(['store', 'update', 'destroy']);
    });
});
