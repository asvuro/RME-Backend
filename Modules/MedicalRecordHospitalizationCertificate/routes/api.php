<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordHospitalizationCertificate\Http\Controllers\HospitalizationCertificateController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('hospitalization-certificates', HospitalizationCertificateController::class)->only(['index', 'show'])->parameters([
        'hospitalization-certificates' => 'certificate',
    ]);

    Route::apiResource('hospitalization-certificates', HospitalizationCertificateController::class)->only(['store', 'update', 'destroy'])->parameters([
    'hospitalization-certificates' => 'certificate',
]);
});
