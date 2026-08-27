<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananMortuaryRecord\Http\Controllers\MortuaryRecordController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('mortuary-records', MortuaryRecordController::class)
        ->only(['index', 'show'])
        ->parameters(['mortuary-records' => 'record']);

    Route::apiResource('mortuary-records', MortuaryRecordController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['mortuary-records' => 'record']);

    Route::post('mortuary-records/{record}/release', [MortuaryRecordController::class, 'release']);
});
