<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPharmacyStatusType\Http\Controllers\PharmacyStatusTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharmacy-status-types', PharmacyStatusTypeController::class)->only(['index', 'show']);

    Route::apiResource('pharmacy-status-types', PharmacyStatusTypeController::class)->only(['store', 'update', 'destroy']);
});
