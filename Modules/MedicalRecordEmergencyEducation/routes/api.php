<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordEmergencyEducation\Http\Controllers\EmergencyEducationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('emergency-educations', EmergencyEducationController::class)->only(['index', 'show'])->parameters(['emergency-educations' => 'record']);

    Route::apiResource('emergency-educations', EmergencyEducationController::class)->only(['store', 'update', 'destroy'])->parameters(['emergency-educations' => 'record']);
});
