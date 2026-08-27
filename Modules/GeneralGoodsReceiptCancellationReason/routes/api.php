<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralGoodsReceiptCancellationReason\Http\Controllers\GoodsReceiptCancellationReasonController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('goods-receipt-cancellation-reasons', GoodsReceiptCancellationReasonController::class)->only(['index', 'show'])->parameters(['goods-receipt-cancellation-reasons' => 'record']);

    Route::apiResource('goods-receipt-cancellation-reasons', GoodsReceiptCancellationReasonController::class)->only(['store', 'update', 'destroy'])->parameters(['goods-receipt-cancellation-reasons' => 'record']);
});
