<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordImplementationNote\Http\Controllers\ImplementationNoteController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('implementation-notes', ImplementationNoteController::class)->only(['index', 'show'])->parameters(['implementation-notes' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('implementation-notes', ImplementationNoteController::class)->only(['store', 'update', 'destroy'])->parameters(['implementation-notes' => 'record']);
    });
});
