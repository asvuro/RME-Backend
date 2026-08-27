<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryStockOpname\Http\Controllers\InventoryStockOpnameController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('inventorystockopnames', InventoryStockOpnameController::class)->only(['index', 'show']);

    Route::apiResource('inventorystockopnames', InventoryStockOpnameController::class)->only(['store', 'update']);
});
