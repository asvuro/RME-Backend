<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordDoctorProcedureConsent\Http\Controllers\DoctorProcedureConsentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('doctor-procedure-consents', DoctorProcedureConsentController::class)->only(['index', 'show'])->parameters(['doctor-procedure-consents' => 'record']);

    Route::apiResource('doctor-procedure-consents', DoctorProcedureConsentController::class)->only(['store'])->parameters(['doctor-procedure-consents' => 'record']);
});
