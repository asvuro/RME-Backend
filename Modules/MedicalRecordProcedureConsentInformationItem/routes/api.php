<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordProcedureConsentInformationItem\Http\Controllers\ProcedureConsentInformationItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('procedure-consent-information-items', ProcedureConsentInformationItemController::class)->only(['index', 'show'])->parameters(['procedure-consent-information-items' => 'record']);

    Route::apiResource('procedure-consent-information-items', ProcedureConsentInformationItemController::class)->only(['store'])->parameters(['procedure-consent-information-items' => 'record']);
});
