<?php

use Illuminate\Support\Facades\Route;
use Modules\PembayaranPayment\Http\Controllers\PaymentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payments', PaymentController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('payments', PaymentController::class)->only(['store']);
    });
});
