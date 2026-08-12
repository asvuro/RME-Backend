<?php

namespace Modules\SystemTteDocument\Services;

use Illuminate\Support\Facades\DB;
use Modules\PendaftaranVisit\Models\Visit;
use Modules\MedicalRecordDischargeSummary\Models\DischargeSummary;
use Modules\SystemTteDocument\Models\TteDocument;

/**
 * Gerbang state machine TTE internal: draft -> pending_sign -> signed -> locked.
 *
 * TIDAK ADA panggilan API eksternal ke PSrE/BSrE di sini -- itu future work.
 * sign() murni menghitung SHA-256 dari `content` (representasi JSON dokumen)
 * dan menandai SIGNED + signed_by + signed_at. lock() mengunci dokumen yang
 * sudah SIGNED supaya tak ada lagi versi baru diterbitkan atas referensi yang
 * sama sampai dokumen itu diarsipkan/diganti secara eksplisit.
 */
class TteDocumentService
{
    /**
     * Referensi yang sah untuk dokumen TTE. ref_type/ref_id TIDAK PERNAH
     * diterima mentah dari klien: setiap entri dipetakan ke kelas model
     * sungguhan supaya create() bisa memverifikasi bahwa baris referensinya
     * benar-benar ada sebelum dokumen draft dibuat (temuan lanjutan vuln-0007:
     * dulu petugas bisa mencetak dokumen "tertandatangani" atas kunjungan
     * orang lain -- atau referensi fiktif yang tidak ada sama sekali).
     */
    public const ALLOWED_REFERENCES = [
        'visits' => Visit::class,
        'medical_record_discharge_summaries' => DischargeSummary::class,
    ];

    /**
     * Buat dokumen TTE baru berstatus draft atas satu referensi.
     *
     * Gerbang: tidak boleh ada dokumen TTE lain yang MASIH AKTIF (draft/
     * pending_sign/signed, belum locked) atas referensi yang sama -- mencegah
     * dua alur tanda tangan berjalan paralel untuk dokumen yang sama.
     */
    public function create(array $data): TteDocument
    {
        return DB::transaction(function () use ($data) {
            $modelClass = self::ALLOWED_REFERENCES[$data['ref_type']] ?? null;

            abort_if(
                $modelClass === null,
                422,
                'ref_type tidak dikenal; dokumen TTE hanya bisa dibuat untuk referensi terdaftar.',
            );

            abort_if(
                ! $modelClass::query()->whereKey($data['ref_id'])->exists(),
                422,
                "Referensi {$data['ref_type']} #{$data['ref_id']} tidak ditemukan.",
            );

            $adaYangAktif = TteDocument::query()
                ->where('ref_type', $data['ref_type'])
                ->where('ref_id', $data['ref_id'])
                ->where('status', '!=', TteDocument::STATUS_LOCKED)
                ->lockForUpdate()
                ->exists();

            abort_if(
                $adaYangAktif,
                422,
                'Sudah ada dokumen TTE aktif (belum locked) untuk referensi ini.',
            );

            return TteDocument::query()->create([
                'ref_type' => $data['ref_type'],
                'ref_id' => $data['ref_id'],
                'status' => TteDocument::STATUS_DRAFT,
                'content' => $data['content'] ?? null,
            ]);
        });
    }

    /**
     * draft -> pending_sign. Menandai dokumen siap ditandatangani; konten
     * dibekukan secara konseptual di sini (perubahan content setelah titik ini
     * seharusnya tidak terjadi, tapi penguncian fisik baru terjadi di sign()
     * lewat document_hash).
     */
    public function submitForSign(int $id): TteDocument
    {
        return DB::transaction(function () use ($id) {
            $document = TteDocument::query()->lockForUpdate()->findOrFail($id);

            abort_if(
                $document->status !== TteDocument::STATUS_DRAFT,
                422,
                "Dokumen #{$id} berstatus {$document->status}, hanya draft yang bisa diajukan tanda tangan.",
            );

            $document->update(['status' => TteDocument::STATUS_PENDING_SIGN]);

            return $document;
        });
    }

    /**
     * pending_sign -> signed. Hitung document_hash = SHA-256 dari JSON
     * `content` saat ini (representasi dokumen "saat submit-for-sign"), catat
     * signed_by + signed_at. Murni internal -- tidak memanggil PSrE/BSrE.
     */
    public function sign(int $id, int $employeeId): TteDocument
    {
        return DB::transaction(function () use ($id, $employeeId) {
            $document = TteDocument::query()->lockForUpdate()->findOrFail($id);

            abort_if(
                $document->status !== TteDocument::STATUS_PENDING_SIGN,
                422,
                "Dokumen #{$id} berstatus {$document->status}, hanya pending_sign yang bisa ditandatangani.",
            );

            $document->update([
                'status' => TteDocument::STATUS_SIGNED,
                'document_hash' => hash('sha256', json_encode($document->content ?? [], JSON_THROW_ON_ERROR)),
                'signed_by' => $employeeId,
                'signed_at' => now(),
            ]);

            return $document->fresh();
        });
    }

    /** signed -> locked. Setelah locked, dokumen final dan tidak bisa diubah lagi. */
    public function lock(int $id): TteDocument
    {
        return DB::transaction(function () use ($id) {
            $document = TteDocument::query()->lockForUpdate()->findOrFail($id);

            abort_if(
                $document->status !== TteDocument::STATUS_SIGNED,
                422,
                "Dokumen #{$id} berstatus {$document->status}, hanya signed yang bisa dikunci.",
            );

            $document->update(['status' => TteDocument::STATUS_LOCKED]);

            return $document;
        });
    }
}
