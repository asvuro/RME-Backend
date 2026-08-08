<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranSelfCheckin\Http\Controllers\SelfCheckinQueueController;

/*
 * ASUMSI DEVICE KIOSK:
 * POST /self-checkin-queues dipakai device Anjungan Pasien Mandiri yang login
 * memakai token SERVICE ACCOUNT milik RS (device token), BUKAN token pasien.
 * Karena itu endpoint ini hanya butuh auth:sanctum biasa dan sengaja TIDAK
 * masuk gate role:petugas|admin -- akun anjungan tidak punya role loket.
 * Gerbang bisnis (anti-duplikat, penomoran) tetap dipaksa di SelfCheckinService.
 */

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Baca: layar monitor loket sekaligus layar "nomor dipanggil" di kiosk,
    // filter ward_id + date (default hari ini -- lihat controller).
    Route::get('self-checkin-queues', [SelfCheckinQueueController::class, 'index']);

    // Check-in dari kiosk (token service account -- lihat catatan di atas).
    // Throttle membatasi laju check-in agar token yang bocor/disalahgunakan
    // tidak bisa membanjiri antrian; batas mengikuti pola endpoint publik
    // mobile-jkn FKTP (throttle:30,1).
    Route::post('self-checkin-queues', [SelfCheckinQueueController::class, 'store'])
        ->middleware('throttle:30,1');

    // Aksi petugas loket: panggil nomor, lalu tandai selesai.
    // Param {queue} sengaja pendek: nama param rute Symfony maksimal 32 karakter.
    Route::middleware('role:petugas|admin')->group(function () {
        Route::post('self-checkin-queues/{queue}/call', [SelfCheckinQueueController::class, 'call']);
        Route::post('self-checkin-queues/{queue}/complete', [SelfCheckinQueueController::class, 'complete']);
    });
});
