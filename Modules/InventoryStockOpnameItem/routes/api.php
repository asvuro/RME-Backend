<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryStockOpnameItem\Http\Controllers\InventoryStockOpnameItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('inventorystockopnameitems', InventoryStockOpnameItemController::class)->only(['index', 'show']);

    Route::apiResource('inventorystockopnameitems', InventoryStockOpnameItemController::class)->only(['store']);
});
