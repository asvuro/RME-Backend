<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananPatientDischargeRecord\Http\Controllers\PatientDischargeRecordController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patient-discharge-records', PatientDischargeRecordController::class)->only(['index', 'show'])->parameters(['patient-discharge-records' => 'discharge_record']);

    Route::apiResource('patient-discharge-records', PatientDischargeRecordController::class)->only(['store', 'update'])->parameters(['patient-discharge-records' => 'discharge_record']);
});
