<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryItem\Http\Controllers\ItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('items', ItemController::class)->only(['index', 'show']);

    Route::apiResource('items', ItemController::class)->only(['store', 'update', 'destroy']);
    Route::post('items/{item}/adjust-stock', [ItemController::class, 'adjustStock']);
});
