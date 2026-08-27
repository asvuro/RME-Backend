<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananAntimicrobialStewardshipPriorHistory\Http\Controllers\AntimicrobialStewardshipPriorHistoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('antimicrobial-stewardship-prior-histories', AntimicrobialStewardshipPriorHistoryController::class)->only(['index', 'show'])->parameters(['antimicrobial-stewardship-prior-histories' => 'amr_history']);

    Route::apiResource('antimicrobial-stewardship-prior-histories', AntimicrobialStewardshipPriorHistoryController::class)->only(['store'])->parameters(['antimicrobial-stewardship-prior-histories' => 'amr_history']);
});
