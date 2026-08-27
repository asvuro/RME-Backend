<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordCatClamsExamination\Http\Controllers\CatClamsExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('cat-clams-examinations', CatClamsExaminationController::class)->only(['index', 'show'])->parameters(['cat-clams-examinations' => 'record']);

    Route::apiResource('cat-clams-examinations', CatClamsExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['cat-clams-examinations' => 'record']);
});
