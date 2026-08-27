<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryGoodsReturnItem\Http\Controllers\InventoryGoodsReturnItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('goods-return-items', InventoryGoodsReturnItemController::class)->only(['index', 'show'])->parameters(['goods-return-items' => 'goods_return_item']);

    Route::apiResource('goods-return-items', InventoryGoodsReturnItemController::class)->only(['store'])->parameters(['goods-return-items' => 'goods_return_item']);
});
