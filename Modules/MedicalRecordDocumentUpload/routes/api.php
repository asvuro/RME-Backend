<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordDocumentUpload\Http\Controllers\DocumentUploadController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('document-uploads', DocumentUploadController::class)->only(['index', 'show'])->parameters([
        'document-uploads' => 'upload',
    ]);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('document-uploads', DocumentUploadController::class)->only(['store', 'update', 'destroy'])->parameters([
        'document-uploads' => 'upload',
    ]);
    });
});
