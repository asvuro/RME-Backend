<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordProcedureConsentInformation\Http\Controllers\ProcedureConsentInformationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('procedure-consent-information', ProcedureConsentInformationController::class)->only(['index', 'show'])->parameters(['procedure-consent-information' => 'record']);

    Route::apiResource('procedure-consent-information', ProcedureConsentInformationController::class)->only(['store'])->parameters(['procedure-consent-information' => 'record']);
});
