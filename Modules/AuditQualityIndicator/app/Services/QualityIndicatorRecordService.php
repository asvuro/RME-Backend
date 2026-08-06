<?php

namespace Modules\AuditQualityIndicator\Services;

use Illuminate\Support\Facades\DB;
use Modules\AuditQualityIndicator\Models\QualityIndicator;
use Modules\AuditQualityIndicator\Models\QualityIndicatorRecord;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;

/**
 * Gerbang pencatatan capaian INM bulanan. Semua tulisan lewat sini supaya
 * dua aturan mutu tidak bisa dilewati controller: periode tidak boleh di masa
 * depan, dan satu indikator hanya boleh punya satu catatan per bulan.
 */
class QualityIndicatorRecordService
{
    /**
     * @param  array<string, mixed>  $data  hasil validasi StoreQualityIndicatorRecordRequest;
     *                               achieved_value sengaja tak diterima dari klien karena dihitung.
     */
    public function save(array $data, User $user): QualityIndicatorRecord
    {
        return DB::transaction(function () use ($data, $user) {
            // Kunci baris master agar dua petugas yang menyimpan periode sama
            // bersamaan tetap diserialisasi sebelum cek duplikat berjalan.
            QualityIndicator::query()->lockForUpdate()->findOrFail($data['indicator_id']);

            $this->assertPeriodNotFuture((int) $data['period_month'], (int) $data['period_year']);
            $this->assertPeriodFree(
                indicatorId: (int) $data['indicator_id'],
                month: (int) $data['period_month'],
                year: (int) $data['period_year'],
            );

            return QualityIndicatorRecord::create([
                ...$data,
                'recorded_by' => $this->resolveRecorder($data['recorded_by'] ?? null, $user),
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $data  hasil validasi UpdateQualityIndicatorRecordRequest
     */
    public function update(QualityIndicatorRecord $record, array $data, User $user): QualityIndicatorRecord
    {
        return DB::transaction(function () use ($record, $data, $user) {
            $month = (int) ($data['period_month'] ?? $record->period_month);
            $year = (int) ($data['period_year'] ?? $record->period_year);

            $this->assertPeriodNotFuture($month, $year);
            $this->assertPeriodFree((int) $record->indicator_id, $month, $year, ignoreId: $record->id);

            $record->update([
                ...$data,
                'period_month' => $month,
                'period_year' => $year,
                'recorded_by' => $this->resolveRecorder($data['recorded_by'] ?? null, $user),
            ]);

            return $record->refresh();
        });
    }

    /**
     * Periode masa depan ditolak: angka capaian bulan yang belum lewat pasti
     * kosong dan hanya membuat tren INM menyesatkan. Bulan berjalan masih
     * dibolehkan (pencatatan progres tengah bulan adalah hal wajar).
     */
    protected function assertPeriodNotFuture(int $month, int $year): void
    {
        abort_if(
            $year > now()->year || ($year === now()->year && $month > now()->month),
            422,
            'Periode catatan mutu tidak boleh di masa depan.',
        );
    }

    /**
     * Hapus catatan capaian (koreksi salah input). Bebas tanpa gerbang khusus:
     * baris ini bukan dokumen legal, dan jejak auditnya tetap tertinggal di
     * activity_logs lewat trait Auditable.
     */
    public function delete(QualityIndicatorRecord $record): void
    {
        DB::transaction(fn () => $record->delete());
    }

    /** Satu catatan per indikator per bulan; duplikat membuat tren ganda. */
    protected function assertPeriodFree(int $indicatorId, int $month, int $year, ?int $ignoreId = null): void
    {
        $duplicate = QualityIndicatorRecord::query()
            ->where('indicator_id', $indicatorId)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->when($ignoreId !== null, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists();

        abort_if(
            $duplicate,
            422,
            "Catatan capaian untuk periode {$month}/{$year} sudah ada pada indikator ini.",
        );
    }

    /**
     * recorded_by menunjuk employees (bukan users). Default: profil pegawai
     * milik user yang sedang login; null bila aktornya belum punya profil —
     * jejak aktor user tetap tercatat oleh activity log (Auditable).
     */
    protected function resolveRecorder(mixed $recordedBy, User $user): ?int
    {
        if (filled($recordedBy)) {
            return (int) $recordedBy;
        }

        return Employee::query()->where('user_id', $user->id)->value('id');
    }
}
