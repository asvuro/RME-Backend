<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralVideoAttachment\Http\Controllers\VideoAttachmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('video-attachments', VideoAttachmentController::class)->only(['index', 'show'])->parameters(['video-attachments' => 'video_attachment']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('video-attachments', VideoAttachmentController::class)->only(['store', 'update', 'destroy'])->parameters(['video-attachments' => 'video_attachment']);
    });
});
