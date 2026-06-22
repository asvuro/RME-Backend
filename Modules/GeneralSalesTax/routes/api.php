<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSalesTax\Http\Controllers\SalesTaxController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sales-taxes', SalesTaxController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('sales-taxes', SalesTaxController::class)->only(['store', 'update', 'destroy']);
    });
});
