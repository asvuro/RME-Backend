<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFamilyPlanningObstetrics\Http\Controllers\FamilyPlanningObstetricsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('family-planning-obstetrics', FamilyPlanningObstetricsController::class)->only(['index', 'show'])->parameters(['family-planning-obstetrics' => 'record']);

    Route::apiResource('family-planning-obstetrics', FamilyPlanningObstetricsController::class)->only(['store', 'update', 'destroy'])->parameters(['family-planning-obstetrics' => 'record']);
});
