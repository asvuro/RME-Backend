<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientFamilyIdentityCard\Http\Controllers\PatientFamilyIdentityCardController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patientfamilyidentitycards', PatientFamilyIdentityCardController::class)->names('generalpatientfamilyidentitycard.patientfamilyidentitycards')->only(['index', 'show']);

    Route::apiResource('patientfamilyidentitycards', PatientFamilyIdentityCardController::class)->names('generalpatientfamilyidentitycard.patientfamilyidentitycards')->only(['store', 'update', 'destroy']);
});
