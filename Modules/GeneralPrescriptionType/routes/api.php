<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPrescriptionType\Http\Controllers\PrescriptionTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('prescription-types', PrescriptionTypeController::class);
});