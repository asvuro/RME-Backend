<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordNursingImplementation\Http\Controllers\NursingImplementationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('nursing-implementations', NursingImplementationController::class)->only(['index', 'show'])->parameters(['nursing-implementations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('nursing-implementations', NursingImplementationController::class)->only(['store', 'update', 'destroy'])->parameters(['nursing-implementations' => 'record']);
    });
});
