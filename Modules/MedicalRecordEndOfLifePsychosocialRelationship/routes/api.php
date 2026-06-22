<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordEndOfLifePsychosocialRelationship\Http\Controllers\EndOfLifePsychosocialRelationshipController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('eol-psychosocial-relationships', EndOfLifePsychosocialRelationshipController::class)->only(['index', 'show'])->parameters(['eol-psychosocial-relationships' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('eol-psychosocial-relationships', EndOfLifePsychosocialRelationshipController::class)->only(['store', 'update', 'destroy'])->parameters(['eol-psychosocial-relationships' => 'record']);
    });
});
