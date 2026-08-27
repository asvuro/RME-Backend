<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryDietOrder\Http\Controllers\DietOrderController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('diet-orders', DietOrderController::class)->only(['index', 'show']);

    Route::apiResource('diet-orders', DietOrderController::class)->only(['store', 'update', 'destroy']);
    Route::patch('diet-orders/{diet_order}/status', [DietOrderController::class, 'transitionStatus']);
});
