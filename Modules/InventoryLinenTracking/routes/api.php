<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryLinenTracking\Http\Controllers\LinenCycleController;
use Modules\InventoryLinenTracking\Http\Controllers\LinenItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('linen-items', LinenItemController::class)->only(['index', 'show']);
    Route::apiResource('linen-cycles', LinenCycleController::class)->only(['index', 'show']);

    Route::apiResource('linen-items', LinenItemController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('linen-cycles', LinenCycleController::class)->only(['store', 'update', 'destroy']);
});
