<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananDrugInteractionCheck\Http\Controllers\DrugInteractionCheckController;
use Modules\LayananDrugInteractionCheck\Http\Controllers\DrugInteractionRuleController;

// Catatan panjang parameter rute: {drug_interaction_rule} = 21 char dan
// {prescription} = 12 char, keduanya di bawah batas 32 char Symfony, jadi
// tidak perlu ->parameters().

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Baca master rule: bebas untuk semua user terautentikasi.
    Route::apiResource('drug-interaction-rules', DrugInteractionRuleController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('drug-interaction-rules', DrugInteractionRuleController::class)->only(['store', 'update', 'destroy']);

        // CDSS advisory read-only: hasil cek tidak pernah mengubah/memblokir
        // alur resep & dispense yang sudah ada.
        Route::get('prescriptions/{prescription}/interaction-check', DrugInteractionCheckController::class)
            ->name('prescriptions.interaction-check');
    });
});
