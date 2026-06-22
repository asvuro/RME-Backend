<?php

use Illuminate\Support\Facades\Route;
use Modules\BerkasKlaimSupportingDocument\Http\Controllers\BerkasKlaimSupportingDocumentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('supporting-documents', BerkasKlaimSupportingDocumentController::class)->only(['index', 'show'])->parameters(['supporting-documents' => 'supporting_document']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('supporting-documents', BerkasKlaimSupportingDocumentController::class)->only(['store', 'update', 'destroy'])->parameters(['supporting-documents' => 'supporting_document']);
    });
});
