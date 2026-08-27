<?php

use Illuminate\Support\Facades\Route;
use Modules\PembayaranCashier\Http\Controllers\CashierController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('cashiers', CashierController::class)->only(['index', 'show']);

    Route::apiResource('cashiers', CashierController::class)->only(['store', 'update', 'destroy']);
});
