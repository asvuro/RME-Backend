<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryItemCategory\Http\Controllers\InventoryItemCategoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('inventoryitemcategories', InventoryItemCategoryController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('inventoryitemcategories', InventoryItemCategoryController::class)->only(['store', 'update', 'destroy']);
    });
});
