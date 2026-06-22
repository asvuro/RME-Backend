<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordClinicalNoteVerification\Http\Controllers\ClinicalNoteVerificationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('clinical-note-verifications', ClinicalNoteVerificationController::class)->only(['index', 'show'])->parameters([
        'clinical-note-verifications' => 'verification',
    ]);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('clinical-note-verifications', ClinicalNoteVerificationController::class)->only(['store', 'update', 'destroy'])->parameters([
        'clinical-note-verifications' => 'verification',
    ]);
    });
});
