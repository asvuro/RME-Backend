<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordHemodialysisLetter\Http\Controllers\HemodialysisLetterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('hemodialysis-letters', HemodialysisLetterController::class)->only(['index', 'show'])->parameters([
        'hemodialysis-letters' => 'letter',
    ]);

    Route::apiResource('hemodialysis-letters', HemodialysisLetterController::class)->only(['store', 'update', 'destroy'])->parameters([
    'hemodialysis-letters' => 'letter',
]);
});
