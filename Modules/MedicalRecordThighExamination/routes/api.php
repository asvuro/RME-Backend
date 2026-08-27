<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordThighExamination\Http\Controllers\ThighExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('thigh-examinations', ThighExaminationController::class)->only(['index', 'show'])->parameters(['thigh-examinations' => 'record']);

    Route::apiResource('thigh-examinations', ThighExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['thigh-examinations' => 'record']);
});
