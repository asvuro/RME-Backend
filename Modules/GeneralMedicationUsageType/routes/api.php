<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralMedicationUsageType\Http\Controllers\MedicationUsageTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('medication-usage-types', MedicationUsageTypeController::class);
});