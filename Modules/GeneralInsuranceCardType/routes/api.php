<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralInsuranceCardType\Http\Controllers\InsuranceCardTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('insurance-card-types', InsuranceCardTypeController::class)->only(['index', 'show']);

    Route::apiResource('insurance-card-types', InsuranceCardTypeController::class)->only(['store', 'update', 'destroy']);
});
