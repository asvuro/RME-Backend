<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientFamily\Http\Controllers\PatientFamilyController;

Route::apiResource('patientfamilies', PatientFamilyController::class)->names('generalpatientfamily.patientfamilies')->only(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('patientfamilies', PatientFamilyController::class)->names('generalpatientfamily.patientfamilies')->only(['store', 'update', 'destroy'])->middleware(['auth:sanctum', 'role:petugas|admin']);
