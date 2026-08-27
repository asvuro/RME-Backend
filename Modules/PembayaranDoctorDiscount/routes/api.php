<?php

use Illuminate\Support\Facades\Route;
use Modules\PembayaranDoctorDiscount\Http\Controllers\DoctorDiscountController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('doctor-discounts', DoctorDiscountController::class)->only(['index', 'show']);

    Route::apiResource('doctor-discounts', DoctorDiscountController::class)->only(['store', 'update', 'destroy']);
});
