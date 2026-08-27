<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryGoodsReturn\Http\Controllers\InventoryGoodsReturnController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('goods-returns', InventoryGoodsReturnController::class)->only(['index', 'show'])->parameters(['goods-returns' => 'goods_return']);

    Route::apiResource('goods-returns', InventoryGoodsReturnController::class)->only(['store', 'update', 'destroy'])->parameters(['goods-returns' => 'goods_return']);
});
