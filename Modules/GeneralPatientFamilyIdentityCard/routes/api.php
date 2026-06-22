<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientFamilyIdentityCard\Http\Controllers\PatientFamilyIdentityCardController;

Route::apiResource('patientfamilyidentitycards', PatientFamilyIdentityCardController::class)->names('generalpatientfamilyidentitycard.patientfamilyidentitycards')->only(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('patientfamilyidentitycards', PatientFamilyIdentityCardController::class)->names('generalpatientfamilyidentitycard.patientfamilyidentitycards')->only(['store', 'update', 'destroy'])->middleware(['auth:sanctum', 'role:petugas|admin']);
