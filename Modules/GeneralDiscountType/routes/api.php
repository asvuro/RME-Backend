<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralDiscountType\Http\Controllers\DiscountTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('discount-types', DiscountTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('discount-types', DiscountTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
