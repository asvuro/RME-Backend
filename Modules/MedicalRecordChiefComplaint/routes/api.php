<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordChiefComplaint\Http\Controllers\ChiefComplaintController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('chief-complaints', ChiefComplaintController::class)->only(['index', 'show'])->parameters(['chief-complaints' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('chief-complaints', ChiefComplaintController::class)->only(['store', 'update', 'destroy'])->parameters(['chief-complaints' => 'record']);
    });
});
