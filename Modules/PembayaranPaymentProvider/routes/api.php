<?php

use Illuminate\Support\Facades\Route;
use Modules\PembayaranPaymentProvider\Http\Controllers\PaymentProviderController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payment-providers', PaymentProviderController::class)->only(['index', 'show'])->parameters(['payment-providers' => 'payment_provider']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('payment-providers', PaymentProviderController::class)->only(['store', 'update', 'destroy'])->parameters(['payment-providers' => 'payment_provider']);
    });
});
