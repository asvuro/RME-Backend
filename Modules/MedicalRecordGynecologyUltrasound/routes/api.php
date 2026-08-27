<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordGynecologyUltrasound\Http\Controllers\GynecologyUltrasoundController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('gynecology-ultrasounds', GynecologyUltrasoundController::class)->only(['index', 'show'])->parameters([
        'gynecology-ultrasounds' => 'ultrasound',
    ]);

    Route::apiResource('gynecology-ultrasounds', GynecologyUltrasoundController::class)->only(['store', 'update', 'destroy'])->parameters([
    'gynecology-ultrasounds' => 'ultrasound',
]);
});
