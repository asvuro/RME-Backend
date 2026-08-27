<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordThroatExamination\Http\Controllers\ThroatExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('throat-examinations', ThroatExaminationController::class)->only(['index', 'show'])->parameters(['throat-examinations' => 'record']);

    Route::apiResource('throat-examinations', ThroatExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['throat-examinations' => 'record']);
});
