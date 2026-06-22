<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananLeftoverMedicationVoucher\Http\Controllers\LeftoverMedicationVoucherController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('leftover-medication-vouchers', LeftoverMedicationVoucherController::class)->only(['index', 'show'])->parameters(['leftover-medication-vouchers' => 'voucher']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('leftover-medication-vouchers', LeftoverMedicationVoucherController::class)->only(['store', 'update'])->parameters(['leftover-medication-vouchers' => 'voucher']);
    });
});
