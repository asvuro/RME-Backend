<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBaepAnxietyDetail\Http\Controllers\BaepAnxietyDetailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('baep-anxiety-details', BaepAnxietyDetailController::class)->only(['index', 'show'])->parameters(['baep-anxiety-details' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('baep-anxiety-details', BaepAnxietyDetailController::class)->only(['store'])->parameters(['baep-anxiety-details' => 'record']);
    });
});
