<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordNursingCarePlan\Http\Controllers\NursingCarePlanController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('nursing-care-plans', NursingCarePlanController::class)->only(['index', 'show'])->parameters(['nursing-care-plans' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('nursing-care-plans', NursingCarePlanController::class)->only(['store', 'update', 'destroy'])->parameters(['nursing-care-plans' => 'record']);
    });
});
