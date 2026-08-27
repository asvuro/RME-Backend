<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordImplementationChecklistItem\Http\Controllers\ImplementationChecklistItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('implementation-checklist-items', ImplementationChecklistItemController::class)->only(['index', 'show'])->parameters(['implementation-checklist-items' => 'record']);

    Route::apiResource('implementation-checklist-items', ImplementationChecklistItemController::class)->only(['store', 'update', 'destroy'])->parameters(['implementation-checklist-items' => 'record']);
});
