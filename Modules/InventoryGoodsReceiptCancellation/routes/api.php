<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryGoodsReceiptCancellation\Http\Controllers\InventoryGoodsReceiptCancellationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('goods-receipt-cancellations', InventoryGoodsReceiptCancellationController::class)->only(['index', 'show'])->parameters(['goods-receipt-cancellations' => 'cancellation']);

    Route::apiResource('goods-receipt-cancellations', InventoryGoodsReceiptCancellationController::class)->only(['store'])->parameters(['goods-receipt-cancellations' => 'cancellation']);
});
