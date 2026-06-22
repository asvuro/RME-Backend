<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordExaminationType\Http\Controllers\ExaminationTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('examination-types', ExaminationTypeController::class)->only(['index', 'show'])->parameters(['examination-types' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('examination-types', ExaminationTypeController::class)->only(['store', 'update', 'destroy'])->parameters(['examination-types' => 'record']);
    });
});
