<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientFamilyContact\Http\Controllers\PatientFamilyContactController;

Route::apiResource('patientfamilycontacts', PatientFamilyContactController::class)->names('generalpatientfamilycontact.patientfamilycontacts')->only(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('patientfamilycontacts', PatientFamilyContactController::class)->names('generalpatientfamilycontact.patientfamilycontacts')->only(['store', 'update', 'destroy'])->middleware(['auth:sanctum', 'role:petugas|admin']);
