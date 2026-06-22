<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananBirthRecord\Http\Controllers\BirthRecordController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('birth-records', BirthRecordController::class)->only(['index', 'show'])->parameters(['birth-records' => 'birth_record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('birth-records', BirthRecordController::class)->only(['store', 'update'])->parameters(['birth-records' => 'birth_record']);
    });
});
