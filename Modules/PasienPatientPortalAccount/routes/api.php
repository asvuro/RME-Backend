<?php

use Illuminate\Support\Facades\Route;
use Modules\PasienPatientPortalAccount\Http\Controllers\PatientPortalAccountController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patient-portal-accounts', PatientPortalAccountController::class)->only(['index', 'show'])->parameters(['patient-portal-accounts' => 'portal_account']);

    Route::apiResource('patient-portal-accounts', PatientPortalAccountController::class)->only(['store', 'update', 'destroy'])->parameters(['patient-portal-accounts' => 'portal_account']);
});
