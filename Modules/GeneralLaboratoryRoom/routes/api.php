<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralLaboratoryRoom\Http\Controllers\GeneralLaboratoryRoomController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('laboratory-rooms', GeneralLaboratoryRoomController::class)->only(['index', 'show'])->parameters(['laboratory-rooms' => 'laboratoryRoom']);

    Route::apiResource('laboratory-rooms', GeneralLaboratoryRoomController::class)->only(['store', 'update', 'destroy'])->parameters(['laboratory-rooms' => 'laboratoryRoom']);
});
