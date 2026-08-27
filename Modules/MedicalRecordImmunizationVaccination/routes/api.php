<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordImmunizationVaccination\Http\Controllers\ImmunizationVaccinationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('immunization-vaccinations', ImmunizationVaccinationController::class)->only(['index', 'show'])->parameters(['immunization-vaccinations' => 'record']);

    Route::apiResource('immunization-vaccinations', ImmunizationVaccinationController::class)->only(['store', 'update', 'destroy'])->parameters(['immunization-vaccinations' => 'record']);
});
