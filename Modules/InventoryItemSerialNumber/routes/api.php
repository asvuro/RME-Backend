<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryItemSerialNumber\Http\Controllers\InventoryItemSerialNumberController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('inventoryitemserialnumbers', InventoryItemSerialNumberController::class)->only(['index', 'show']);

    Route::apiResource('inventoryitemserialnumbers', InventoryItemSerialNumberController::class)->only(['store', 'update', 'destroy']);
});
