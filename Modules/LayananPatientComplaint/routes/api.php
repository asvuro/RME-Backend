<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananPatientComplaint\Http\Controllers\PatientComplaintController;
use Modules\LayananPatientComplaint\Http\Controllers\PatientSurveyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Catatan urutan: summary HARUS terdaftar sebelum apiResource, kalau tidak
    // URI "patient-complaints/summary" tertangkap route show ({patient_complaint}).
    Route::get('patient-complaints/summary', [PatientComplaintController::class, 'summary']);

    // Baca-saja: semua pengguna terautentikasi.
    Route::apiResource('patient-complaints', PatientComplaintController::class)->only(['index', 'show']);
    Route::apiResource('patient-surveys', PatientSurveyController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('patient-complaints', PatientComplaintController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('patient-surveys', PatientSurveyController::class)->only(['store', 'update', 'destroy']);
    });
});
