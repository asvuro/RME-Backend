<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordImplementation\Http\Controllers\ImplementationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('implementations', ImplementationController::class)->only(['index', 'show'])->parameters(['implementations' => 'record']);

    Route::apiResource('implementations', ImplementationController::class)->only(['store', 'update', 'destroy'])->parameters(['implementations' => 'record']);
});
