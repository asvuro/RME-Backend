<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranAccidentRecord\Http\Controllers\AccidentRecordController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('accidentrecords', AccidentRecordController::class)->only(['index', 'show']);

    Route::apiResource('accidentrecords', AccidentRecordController::class)->only(['store']);
});
