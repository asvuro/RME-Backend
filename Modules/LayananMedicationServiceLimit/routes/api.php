<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananMedicationServiceLimit\Http\Controllers\MedicationServiceLimitController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('medication-service-limits', MedicationServiceLimitController::class)->only(['index', 'show'])->parameters(['medication-service-limits' => 'service_limit']);

    Route::apiResource('medication-service-limits', MedicationServiceLimitController::class)->only(['store', 'update', 'destroy'])->parameters(['medication-service-limits' => 'service_limit']);
});
