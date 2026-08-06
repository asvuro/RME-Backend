<?php

namespace Modules\AuditInfectionSurveillance\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\AuditInfectionSurveillance\Models\DeviceDay;
use Modules\AuditInfectionSurveillance\Models\InfectionCase;

/**
 * Surveilans PPI (HAIs) — pencatatan hari-alat, kasus infeksi, dan kalkulasi
 * laju infeksi. Semua penulisan data lewat sini agar gerbang konsistensi
 * (rentang pasang/lepas, rujukan kasus, kesesuaian jenis alat) tidak bisa
 * dilewati controller mana pun.
 *
 * Asumsi desain:
 *  - Denominator facility-wide: hari-alat dihitung dari SEMUA kunjungan pada
 *    periode, bukan per kunjungan (angka IR memang dilaporkan per unit/RS).
 *  - Pemetaan penyakit→alat mengikuti konvensi NHSN yang disederhanakan:
 *    ISK→kateter urin, plebitis→infus IV, VAP→ventilator. IDO (infeksi
 *    daerah operasi) tidak punya satu alat payung, jadi dipakai total semua
 *    hari-alat sebagai proksi denominator karena modul ini belum memiliki
 *    tabel prosedur operasi.
 *  - Hari-alat dihitung per kalender: sebagian apa pun dari suatu tanggal
 *    di dalam periode tetap dihitung 1 hari.
 *  - Alat yang masih terpasang (removed_at NULL) dihitung sampai batas lebih
 *    awal antara akhir periode dan hari ini — masa depan tidak diproyeksikan.
 */
class SurveillanceService
{
    /**
     * Jenis alat denominator untuk tiap jenis infeksi; NULL = gabungan semua alat.
     *
     * @var array<string, string|null>
     */
    protected const DEVICE_TYPE_BY_INFECTION = [
        InfectionCase::TYPE_ISK => DeviceDay::TYPE_KATETER_URINE,
        InfectionCase::TYPE_PLEBITIS => DeviceDay::TYPE_INFUS_IV,
        InfectionCase::TYPE_VAP => DeviceDay::TYPE_VENTILATOR,
        InfectionCase::TYPE_IDO => null,
    ];

    // ------------------------------------------------------------------
    // DeviceDay
    // ------------------------------------------------------------------

    /**
     * @param  array<string, mixed>  $data  hasil validasi StoreDeviceDayRequest
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException gerbang menolak (422)
     */
    public function createDeviceDay(array $data): DeviceDay
    {
        $this->assertDevicePeriod(
            Carbon::parse($data['inserted_at']),
            isset($data['removed_at']) ? Carbon::parse($data['removed_at']) : null,
        );

        return DB::transaction(fn () => DeviceDay::create($data));
    }

    /**
     * @param  array<string, mixed>  $data  hasil validasi UpdateDeviceDayRequest
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException gerbang menolak (422)
     */
    public function updateDeviceDay(DeviceDay $deviceDay, array $data): DeviceDay
    {
        $insertedAt = isset($data['inserted_at'])
            ? Carbon::parse($data['inserted_at'])
            : $deviceDay->inserted_at;

        $removedAt = array_key_exists('removed_at', $data)
            ? ($data['removed_at'] !== null ? Carbon::parse($data['removed_at']) : null)
            : $deviceDay->removed_at;

        // after_or_equal di FormRequest tidak bisa melihat nilai lama; rentang
        // gabungan lama+baru hanya benar dicek di sini.
        $this->assertDevicePeriod($insertedAt, $removedAt);

        return DB::transaction(function () use ($deviceDay, $data) {
            $deviceDay->update($data);

            return $deviceDay->refresh();
        });
    }

    /**
     * Hapus hari-alat. Ditolak bila masih dirujuk kasus infeksi: rujukan
     * epidemiologis (kasus → hari-alat) tidak boleh putus diam-diam,
     * petugas harus memutus rujukannya lebih dulu.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException gerbang menolak (422)
     */
    public function deleteDeviceDay(DeviceDay $deviceDay): void
    {
        DB::transaction(function () use ($deviceDay) {
            $dirujuk = InfectionCase::query()
                ->where('related_device_day_id', $deviceDay->id)
                ->exists();

            abort_if(
                $dirujuk,
                422,
                'Hari-alat masih dirujuk kasus infeksi; perbarui rujukan kasus dahulu.',
            );

            $deviceDay->delete();
        });
    }

    // ------------------------------------------------------------------
    // InfectionCase
    // ------------------------------------------------------------------

    /**
     * @param  array<string, mixed>  $data  hasil validasi StoreInfectionCaseRequest
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException gerbang menolak (422)
     */
    public function createInfectionCase(array $data): InfectionCase
    {
        $this->assertCaseConsistency(
            (int) $data['visit_id'],
            $data['infection_type'],
            $data['related_device_day_id'] ?? null,
            Carbon::parse($data['diagnosed_at']),
        );

        return DB::transaction(fn () => InfectionCase::create($data));
    }

    /**
     * @param  array<string, mixed>  $data  hasil validasi UpdateInfectionCaseRequest
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException gerbang menolak (422)
     */
    public function updateInfectionCase(InfectionCase $case, array $data): InfectionCase
    {
        $diagnosedAt = isset($data['diagnosed_at'])
            ? Carbon::parse($data['diagnosed_at'])
            : $case->diagnosed_at;

        $this->assertCaseConsistency(
            (int) ($data['visit_id'] ?? $case->visit_id),
            $data['infection_type'] ?? $case->infection_type,
            array_key_exists('related_device_day_id', $data) ? $data['related_device_day_id'] : $case->related_device_day_id,
            $diagnosedAt,
        );

        return DB::transaction(function () use ($case, $data) {
            $case->update($data);

            return $case->refresh();
        });
    }

    public function deleteInfectionCase(InfectionCase $case): void
    {
        DB::transaction(fn () => $case->delete());
    }

    // ------------------------------------------------------------------
    // Kalkulasi laju infeksi
    // ------------------------------------------------------------------

    /**
     * Laju infeksi per 1.000 hari-alat untuk satu jenis infeksi:
     * rate = (kasus / hari-alat aktif periode) x 1000. Pembagi nol
     * dikembalikan sebagai 0 (bukan error/INF) — periode tanpa penggunaan
     * alat itu sah, hanya memang tidak bisa dinormalisasi.
     *
     * @return array{cases: int, deviceDays: int, ratePer1000: float|int}
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException jenis infeksi/periode tidak valid (422)
     */
    public function calculateRate(string $infectionType, Carbon $periodStart, Carbon $periodEnd): array
    {
        abort_if(
            ! array_key_exists($infectionType, self::DEVICE_TYPE_BY_INFECTION),
            422,
            'Jenis infeksi tidak dikenal.',
        );
        abort_if(
            $periodEnd->copy()->startOfDay()->lt($periodStart->copy()->startOfDay()),
            422,
            'Akhir periode tidak boleh sebelum awal periode.',
        );

        $cases = InfectionCase::query()
            ->where('infection_type', $infectionType)
            ->whereBetween('diagnosed_at', [
                $periodStart->copy()->startOfDay(),
                $periodEnd->copy()->endOfDay(),
            ])
            ->count();

        $deviceDays = $this->countDeviceDays(
            self::DEVICE_TYPE_BY_INFECTION[$infectionType],
            $periodStart,
            $periodEnd,
        );

        $rate = $deviceDays > 0 ? round(($cases / $deviceDays) * 1000, 2) : 0;

        return [
            'cases' => $cases,
            'deviceDays' => $deviceDays,
            'ratePer1000' => $rate,
        ];
    }

    // ------------------------------------------------------------------
    // Gerbang internal
    // ------------------------------------------------------------------

    /** Rentang pasang/lepas alat harus masuk akal dan tidak di masa depan. */
    protected function assertDevicePeriod(Carbon $insertedAt, ?Carbon $removedAt): void
    {
        abort_if($insertedAt->isFuture(), 422, 'Tanggal pasang alat tidak boleh di masa depan.');
        abort_if(
            $removedAt !== null && $removedAt->lt($insertedAt),
            422,
            'Tanggal lepas alat tidak boleh sebelum tanggal pasang.',
        );
    }

    /**
     * Konsistensi kasus: diagnosis tidak di masa depan; rujukan hari-alat
     * (bila ada) harus milik kunjungan yang sama dan sesuai jenis alat
     * denominatornya (kasus ISK merujuk kateter urin, bukan ventilator).
     * Tanpa gerbang ini numerator dan denominator bisa berasal dari pasien
     * berbeda sehingga angka IR melenceng secara diam-diam.
     */
    protected function assertCaseConsistency(int $visitId, string $infectionType, mixed $deviceDayId, Carbon $diagnosedAt): void
    {
        abort_if($diagnosedAt->isFuture(), 422, 'Tanggal diagnosis tidak boleh di masa depan.');

        if ($deviceDayId === null) {
            return; // kasus tanpa rujukan alat sah (mis. IDO dari luka operasi).
        }

        $deviceDay = DeviceDay::query()->find((int) $deviceDayId);

        abort_if($deviceDay === null, 422, "Hari-alat #{$deviceDayId} tidak dikenal.");
        abort_if(
            (int) $deviceDay->visit_id !== $visitId,
            422,
            'Kasus infeksi tidak boleh merujuk hari-alat dari kunjungan lain.',
        );

        $expectedType = self::DEVICE_TYPE_BY_INFECTION[$infectionType] ?? null;

        abort_if(
            $expectedType !== null && $deviceDay->device_type !== $expectedType,
            422,
            "Jenis alat pada hari-alat #{$deviceDayId} tidak sesuai untuk infeksi {$infectionType}.",
        );
    }

    /**
     * Total hari-alat aktif pada periode [start, end] inklusif, granularitas
     * kalender (sebagian hari tetap dihitung 1 hari).
     *
     * @param  string|null  $deviceType  NULL = gabungan semua jenis alat
     */
    protected function countDeviceDays(?string $deviceType, Carbon $periodStart, Carbon $periodEnd): int
    {
        $start = $periodStart->copy()->startOfDay();
        $end = $periodEnd->copy()->endOfDay()->min(now());

        if ($end->lessThan($start)) {
            return 0; // seluruh periode berada di masa depan relatif ke hari ini.
        }

        $cap = $end->copy()->startOfDay();
        $total = 0;

        DeviceDay::query()
            ->when($deviceType !== null, fn ($q) => $q->where('device_type', $deviceType))
            // Pra-filter SQL: baris yang sama sekali tidak beririsan dengan
            // periode dilewati sebelum hitung irisan per baris.
            ->where('inserted_at', '<=', $end)
            ->where(fn ($q) => $q->whereNull('removed_at')->orWhere('removed_at', '>=', $start))
            ->chunkById(500, function ($rows) use (&$total, $start, $cap): void {
                foreach ($rows as $row) {
                    $from = $row->inserted_at->copy()->startOfDay()->max($start);
                    $to = ($row->removed_at !== null ? $row->removed_at->copy()->startOfDay() : $cap)->min($cap);

                    if ($to->greaterThanOrEqualTo($from)) {
                        $total += (int) $from->diffInDays($to) + 1;
                    }
                }
            });

        return $total;
    }
}
