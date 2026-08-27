<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBaepDysphagiaDetail\Http\Controllers\BaepDysphagiaDetailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('baep-dysphagia-details', BaepDysphagiaDetailController::class)->only(['index', 'show'])->parameters(['baep-dysphagia-details' => 'record']);

    Route::apiResource('baep-dysphagia-details', BaepDysphagiaDetailController::class)->only(['store'])->parameters(['baep-dysphagia-details' => 'record']);
});
