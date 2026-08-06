<?php

namespace Modules\AuditQualityIndicator\Services;

use Illuminate\Support\Facades\DB;
use Modules\AuditQualityIndicator\Models\QualityIndicator;

/**
 * Gerbang master Indikator Mutu. Tulis master HANYA lewat sini agar gerbang
 * hapus (riwayat capaian masih ada) tidak bisa dilewati controller.
 */
class QualityIndicatorService
{
    /**
     * @param  array<string, mixed>  $data  hasil validasi StoreQualityIndicatorRequest
     */
    public function create(array $data): QualityIndicator
    {
        return DB::transaction(fn () => QualityIndicator::create($data));
    }

    /**
     * @param  array<string, mixed>  $data  hasil validasi UpdateQualityIndicatorRequest
     */
    public function update(QualityIndicator $indicator, array $data): QualityIndicator
    {
        return DB::transaction(function () use ($indicator, $data) {
            $indicator->update($data);

            return $indicator->refresh();
        });
    }

    /**
     * Hapus indikator ditolak 422 bila masih punya catatan capaian: riwayat
     * INM adalah bukti mutu/akreditasi yang tidak boleh ikut terhapus hanya
     * karena definisi indikatornya ditarik. Kolom FK memang restrictOnDelete,
     * tapi pesan gerbang di sini yang menjelaskan jalan keluarnya ke petugas.
     */
    public function delete(QualityIndicator $indicator): void
    {
        DB::transaction(function () use ($indicator) {
            abort_if(
                $indicator->records()->exists(),
                422,
                'Indikator masih memiliki catatan capaian; hapus catatan periodenya lebih dulu.',
            );

            $indicator->delete();
        });
    }
}
