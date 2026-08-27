<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordProcedureConsentInformationReceiver\Http\Controllers\ProcedureConsentInformationReceiverController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('procedure-consent-information-receivers', ProcedureConsentInformationReceiverController::class)->only(['index', 'show'])->parameters(['procedure-consent-information-receivers' => 'record']);

    Route::apiResource('procedure-consent-information-receivers', ProcedureConsentInformationReceiverController::class)->only(['store'])->parameters(['procedure-consent-information-receivers' => 'record']);
});
