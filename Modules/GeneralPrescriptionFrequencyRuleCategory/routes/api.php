<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPrescriptionFrequencyRuleCategory\Http\Controllers\PrescriptionFrequencyRuleCategoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('prescription-frequency-rule-categories', PrescriptionFrequencyRuleCategoryController::class)->only(['index', 'show'])->parameters(['prescription-frequency-rule-categories' => 'freq_rule_category']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('prescription-frequency-rule-categories', PrescriptionFrequencyRuleCategoryController::class)->only(['store', 'update', 'destroy'])->parameters(['prescription-frequency-rule-categories' => 'freq_rule_category']);
    });
});
