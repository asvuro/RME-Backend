<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPaymentTransactionType\Http\Controllers\PaymentTransactionTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payment-transaction-types', PaymentTransactionTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('payment-transaction-types', PaymentTransactionTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
