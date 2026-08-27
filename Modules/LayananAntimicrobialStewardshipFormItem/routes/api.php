<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananAntimicrobialStewardshipFormItem\Http\Controllers\AntimicrobialStewardshipFormItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('antimicrobial-stewardship-form-items', AntimicrobialStewardshipFormItemController::class)->only(['index', 'show'])->parameters(['antimicrobial-stewardship-form-items' => 'amr_item']);

    Route::apiResource('antimicrobial-stewardship-form-items', AntimicrobialStewardshipFormItemController::class)->only(['store'])->parameters(['antimicrobial-stewardship-form-items' => 'amr_item']);
});
