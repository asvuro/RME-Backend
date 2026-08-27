<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBirthCertificateLetter\Http\Controllers\BirthCertificateLetterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('birth-certificate-letters', BirthCertificateLetterController::class)->only(['index', 'show'])->parameters([
        'birth-certificate-letters' => 'letter',
    ]);

    Route::apiResource('birth-certificate-letters', BirthCertificateLetterController::class)->only(['store', 'update', 'destroy'])->parameters([
    'birth-certificate-letters' => 'letter',
]);
});
