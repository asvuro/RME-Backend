<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananTreatmentProtocolStepDrug\Http\Controllers\TreatmentProtocolStepDrugController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('treatment-protocol-step-drugs', TreatmentProtocolStepDrugController::class)->only(['index', 'show'])->parameters(['treatment-protocol-step-drugs' => 'record']);

    Route::apiResource('treatment-protocol-step-drugs', TreatmentProtocolStepDrugController::class)->only(['store', 'update', 'destroy'])->parameters(['treatment-protocol-step-drugs' => 'record']);
});
