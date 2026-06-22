<?php

use Illuminate\Support\Facades\Route;
use Modules\PenjualanSale\Http\Controllers\SaleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sales', SaleController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sales', SaleController::class)->only(['store', 'update']);
    });
});
