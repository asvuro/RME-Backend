<?php

use Illuminate\Support\Facades\Route;
use Modules\CetakanPrintDocument\Http\Controllers\PrintDocumentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('print-documents', [PrintDocumentController::class, 'index']);
    Route::post('print-documents/issue', [PrintDocumentController::class, 'issue']);
    Route::get('print-documents/{document}', [PrintDocumentController::class, 'show']);
});
