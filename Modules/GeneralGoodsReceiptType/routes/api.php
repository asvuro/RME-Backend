<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralGoodsReceiptType\Http\Controllers\GoodsReceiptTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('goods-receipt-types', GoodsReceiptTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('goods-receipt-types', GoodsReceiptTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
