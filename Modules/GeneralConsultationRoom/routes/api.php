<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralConsultationRoom\Http\Controllers\GeneralConsultationRoomController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('consultation-rooms', GeneralConsultationRoomController::class)->only(['index', 'show'])->parameters(['consultation-rooms' => 'consultationRoom']);

    Route::apiResource('consultation-rooms', GeneralConsultationRoomController::class)->only(['store', 'update', 'destroy'])->parameters(['consultation-rooms' => 'consultationRoom']);
});
