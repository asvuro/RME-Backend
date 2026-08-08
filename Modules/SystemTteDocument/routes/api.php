<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemTteDocument\Http\Controllers\TteDocumentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('tte-documents', [TteDocumentController::class, 'index']);
    Route::get('tte-documents/{tteDocument}', [TteDocumentController::class, 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::post('tte-documents', [TteDocumentController::class, 'store']);
        Route::post('tte-documents/{tteDocument}/submit-for-sign', [TteDocumentController::class, 'submitForSign']);
        Route::post('tte-documents/{tteDocument}/sign', [TteDocumentController::class, 'sign']);
    });

    // lock() menyegel dokumen secara permanen (final, tidak bisa diubah lagi) --
    // dibatasi admin saja supaya penanda tangan (petugas) tidak bisa langsung
    // mengunci tanda tangannya sendiri jadi tak tergugat.
    Route::middleware('role:admin')->group(function () {
        Route::post('tte-documents/{tteDocument}/lock', [TteDocumentController::class, 'lock']);
    });
});
