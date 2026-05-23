<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralGoodsReceiptCancellationReason\Http\Controllers\GoodsReceiptCancellationReasonController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('goods-receipt-cancellation-reasons', GoodsReceiptCancellationReasonController::class)->parameters(['goods-receipt-cancellation-reasons' => 'record']);
});