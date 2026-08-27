<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananImagingOrder\Http\Controllers\ImagingOrderController;
use Modules\LayananImagingOrder\Http\Controllers\ImagingStudyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Baca-saja: terbuka untuk semua user terautentikasi.
    Route::apiResource('imaging-orders', ImagingOrderController::class)
        ->only(['index', 'show'])
        ->parameters(['imaging-orders' => 'imaging_order']);

    Route::apiResource('imaging-studies', ImagingStudyController::class)
        ->only(['index', 'show'])
        ->parameters(['imaging-studies' => 'imaging_study']);

    Route::apiResource('imaging-orders', ImagingOrderController::class)
        ->only(['store', 'update'])
        ->parameters(['imaging-orders' => 'imaging_order']);

    // Jadwal & batal lewat gerbang khusus, bukan edit bebas
    // (pola yang sama dengan transfer/discharge di VisitController).
    Route::post('imaging-orders/{imaging_order}/schedule', [ImagingOrderController::class, 'schedule']);
    Route::post('imaging-orders/{imaging_order}/cancel', [ImagingOrderController::class, 'cancel']);

    Route::apiResource('imaging-studies', ImagingStudyController::class)
        ->only(['store', 'update'])
        ->parameters(['imaging-studies' => 'imaging_study']);
});
