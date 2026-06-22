<?php

use Illuminate\Support\Facades\Route;
use Modules\BpjsICare\Http\Controllers\RiwayatPelayananController;

Route::middleware(['auth:sanctum', 'role:petugas|admin'])->prefix('v1')->group(function () {
    // "API Data Riwayat Pelayanan" - cross-facility care history lookup (FKRTL signature variant).
    Route::post('riwayat-pelayanan/validate', [RiwayatPelayananController::class, 'validate']);
});
