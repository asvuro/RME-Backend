<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralAudioAttachment\Http\Controllers\AudioAttachmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('audio-attachments', AudioAttachmentController::class)->only(['index', 'show'])->parameters(['audio-attachments' => 'audio_attachment']);

    Route::apiResource('audio-attachments', AudioAttachmentController::class)->only(['store', 'update', 'destroy'])->parameters(['audio-attachments' => 'audio_attachment']);
});
