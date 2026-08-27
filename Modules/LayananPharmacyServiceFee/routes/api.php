<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananPharmacyServiceFee\Http\Controllers\PharmacyServiceFeeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharmacy-service-fees', PharmacyServiceFeeController::class)->only(['index', 'show'])->parameters(['pharmacy-service-fees' => 'service_fee']);

    Route::apiResource('pharmacy-service-fees', PharmacyServiceFeeController::class)->only(['store', 'update', 'destroy'])->parameters(['pharmacy-service-fees' => 'service_fee']);
});
