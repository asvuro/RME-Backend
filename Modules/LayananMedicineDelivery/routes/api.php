<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananMedicineDelivery\Http\Controllers\MedicineDeliveryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // parameters() dipakai agar nama param route pendek ('delivery') -
    // nama param default turunan resource bisa panjang (jebakan >32 char
    // di binding Symfony yang pernah kejadian di proyek ini).
    Route::apiResource('medicine-deliveries', MedicineDeliveryController::class)
        ->only(['index', 'show'])
        ->parameters(['medicine-deliveries' => 'delivery']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('medicine-deliveries', MedicineDeliveryController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['medicine-deliveries' => 'delivery']);

        // Gerbang kurir: penugasan (paket berangkat) dan serah terima.
        Route::post('medicine-deliveries/{delivery}/assign-courier', [MedicineDeliveryController::class, 'assignCourier']);
        Route::post('medicine-deliveries/{delivery}/mark-delivered', [MedicineDeliveryController::class, 'markDelivered']);
    });
});
