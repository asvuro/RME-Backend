<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordSurgeryPerformer\Http\Controllers\SurgeryPerformerController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('surgery-performers', SurgeryPerformerController::class)->only(['index', 'show'])->parameters(['surgery-performers' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('surgery-performers', SurgeryPerformerController::class)->only(['store', 'update', 'destroy'])->parameters(['surgery-performers' => 'record']);
    });
});
