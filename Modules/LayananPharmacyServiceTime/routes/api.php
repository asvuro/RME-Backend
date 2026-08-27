<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananPharmacyServiceTime\Http\Controllers\PharmacyServiceTimeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharmacy-service-times', PharmacyServiceTimeController::class)->only(['index', 'show'])->parameters(['pharmacy-service-times' => 'record']);

    Route::apiResource('pharmacy-service-times', PharmacyServiceTimeController::class)->only(['store', 'update', 'destroy'])->parameters(['pharmacy-service-times' => 'record']);
});
