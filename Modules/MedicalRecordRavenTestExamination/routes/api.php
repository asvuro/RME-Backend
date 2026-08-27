<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordRavenTestExamination\Http\Controllers\RavenTestExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('raven-test-examinations', RavenTestExaminationController::class)->only(['index', 'show'])->parameters(['raven-test-examinations' => 'record']);

    Route::apiResource('raven-test-examinations', RavenTestExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['raven-test-examinations' => 'record']);
});
