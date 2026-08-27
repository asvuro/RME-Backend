<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralScannedDocument\Http\Controllers\GeneralScannedDocumentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('scanned-documents', GeneralScannedDocumentController::class)->only(['index', 'show'])->parameters(['scanned-documents' => 'scannedDocument']);

    Route::apiResource('scanned-documents', GeneralScannedDocumentController::class)->only(['store'])->parameters(['scanned-documents' => 'scannedDocument']);
});
