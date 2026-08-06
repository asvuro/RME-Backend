<?php

use Illuminate\Support\Facades\Route;
use Modules\InventorySterilizationCycle\Http\Controllers\SterilizationCycleController;
use Modules\InventorySterilizationCycle\Http\Controllers\SterilizedItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sterilization-cycles', SterilizationCycleController::class)->only(['index', 'show']);
    Route::apiResource('sterilized-items', SterilizedItemController::class)->only(['index', 'show']);
    Route::get('sterilized-items/{sterilized_item}/check-expiry', [SterilizedItemController::class, 'checkExpiry']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sterilization-cycles', SterilizationCycleController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('sterilized-items', SterilizedItemController::class)->only(['store', 'update', 'destroy']);
    });
});
