<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBaepCognitiveDetail\Http\Controllers\BaepCognitiveDetailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('baep-cognitive-details', BaepCognitiveDetailController::class)->only(['index', 'show'])->parameters(['baep-cognitive-details' => 'record']);

    Route::apiResource('baep-cognitive-details', BaepCognitiveDetailController::class)->only(['store'])->parameters(['baep-cognitive-details' => 'record']);
});
