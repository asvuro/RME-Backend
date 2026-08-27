<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBaepInsomniaDetail\Http\Controllers\BaepInsomniaDetailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('baep-insomnia-details', BaepInsomniaDetailController::class)->only(['index', 'show'])->parameters(['baep-insomnia-details' => 'record']);

    Route::apiResource('baep-insomnia-details', BaepInsomniaDetailController::class)->only(['store'])->parameters(['baep-insomnia-details' => 'record']);
});
