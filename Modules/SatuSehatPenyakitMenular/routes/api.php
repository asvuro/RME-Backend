<?php

use Illuminate\Support\Facades\Route;
use Modules\SatuSehatPenyakitMenular\Http\Controllers\SatuSehatPenyakitMenularController;

Route::middleware(['auth:sanctum', 'role:petugas|admin'])->prefix('v1/satu-sehat-penyakit-menular')->group(function () {
    Route::get('/', [SatuSehatPenyakitMenularController::class, 'index']);
    Route::get('{penyakitMenularSubmission}', [SatuSehatPenyakitMenularController::class, 'show']);
    Route::post('{useCase}', [SatuSehatPenyakitMenularController::class, 'store'])->whereIn('useCase', ['tuberkulosis', 'hiv', 'rabies', 'anthrax', 'smpk']);
});
