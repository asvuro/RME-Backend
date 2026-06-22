<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordInhalantAllergenExamination\Http\Controllers\InhalantAllergenExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('inhalant-allergen-examinations', InhalantAllergenExaminationController::class)->only(['index', 'show'])->parameters(['inhalant-allergen-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('inhalant-allergen-examinations', InhalantAllergenExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['inhalant-allergen-examinations' => 'record']);
    });
});
