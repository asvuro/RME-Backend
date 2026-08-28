<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientFamilyContact\Http\Controllers\PatientFamilyContactController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patientfamilycontacts', PatientFamilyContactController::class)->names('generalpatientfamilycontact.patientfamilycontacts')->only(['index', 'show']);

    Route::apiResource('patientfamilycontacts', PatientFamilyContactController::class)->names('generalpatientfamilycontact.patientfamilycontacts')->only(['store', 'update', 'destroy']);
});
