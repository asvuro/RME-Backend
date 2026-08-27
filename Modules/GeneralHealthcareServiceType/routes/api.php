<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralHealthcareServiceType\Http\Controllers\HealthcareServiceTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('healthcare-service-types', HealthcareServiceTypeController::class)->only(['index', 'show']);

    Route::apiResource('healthcare-service-types', HealthcareServiceTypeController::class)->only(['store', 'update', 'destroy']);
});
