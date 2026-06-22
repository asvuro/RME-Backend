<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBaepStimulationProtocolDetail\Http\Controllers\BaepStimulationProtocolDetailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('baep-stimulation-protocol-details', BaepStimulationProtocolDetailController::class)->only(['index', 'show'])->parameters(['baep-stimulation-protocol-details' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('baep-stimulation-protocol-details', BaepStimulationProtocolDetailController::class)->only(['store'])->parameters(['baep-stimulation-protocol-details' => 'record']);
    });
});
