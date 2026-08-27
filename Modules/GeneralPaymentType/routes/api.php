<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPaymentType\Http\Controllers\PaymentTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payment-types', PaymentTypeController::class)->only(['index', 'show']);

    Route::apiResource('payment-types', PaymentTypeController::class)->only(['store', 'update', 'destroy']);
});
