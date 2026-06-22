<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFoodAllergenExamination\Http\Controllers\FoodAllergenExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('food-allergen-examinations', FoodAllergenExaminationController::class)->only(['index', 'show'])->parameters(['food-allergen-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('food-allergen-examinations', FoodAllergenExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['food-allergen-examinations' => 'record']);
    });
});
