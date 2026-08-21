<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemLicenseGuard\Http\Controllers\LicenseController;

// Gerbang peran role:admin lama sudah digantikan RoutePermissionGate global
// (RBAC dinamis, per-aksi) -- lihat rbac-dynamic-permission-plan.
Route::prefix('v1/system/license')->group(function () {
    // HWID dan metadata lisensi hanya untuk operator berwenang; tanpa gate ini
    // siapa pun bisa mengambil fingerprint hardware yang menjadi kunci anti-kloning.
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/status', [LicenseController::class, 'status'])->middleware('throttle:60,1');
        Route::get('/fingerprint', [LicenseController::class, 'fingerprint'])->middleware('throttle:60,1');
    });
    Route::post('/activate', [LicenseController::class, 'activate'])->middleware('throttle:10,1');
    Route::post('/sync', [LicenseController::class, 'sync'])->middleware('throttle:10,1');
    Route::post('/webhook', [LicenseController::class, 'webhook'])->middleware('throttle:30,1');
});
