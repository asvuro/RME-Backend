<?php

use Illuminate\Support\Facades\Route;
use Modules\AplikasiSetting\Http\Controllers\RsSettingController;

/*
 * Port REST PropertiConfig simgos2 (Aplikasi/V1/Rest/PropertiConfig):
 * baca terbuka bagi pengguna terautentikasi, tulis hanya admin —
 * di aslinya tabel ini mengubah perilaku gerbang bisnis seluruh RS.
 */

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('settings', [RsSettingController::class, 'index']);
        Route::get('settings/{key}', [RsSettingController::class, 'show']);

        Route::middleware('role:admin')->group(function () {
            Route::post('settings', [RsSettingController::class, 'store']);
            Route::put('settings/{key}', [RsSettingController::class, 'update']);
            Route::patch('settings/{key}', [RsSettingController::class, 'update']);
        });
    });
});
