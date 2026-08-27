<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryShipmentItem\Http\Controllers\ShipmentItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('shipment-items', ShipmentItemController::class)->only(['index', 'show']);

    Route::apiResource('shipment-items', ShipmentItemController::class)->only(['store']);
});
