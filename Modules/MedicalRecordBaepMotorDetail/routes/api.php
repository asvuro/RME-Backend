<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBaepMotorDetail\Http\Controllers\BaepMotorDetailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('baep-motor-details', BaepMotorDetailController::class)->only(['index', 'show'])->parameters(['baep-motor-details' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('baep-motor-details', BaepMotorDetailController::class)->only(['store'])->parameters(['baep-motor-details' => 'record']);
    });
});
