<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralMixtureInstruction\Http\Controllers\MixtureInstructionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('mixture-instructions', MixtureInstructionController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('mixture-instructions', MixtureInstructionController::class)->only(['store', 'update', 'destroy']);
    });
});
