<?php

namespace Modules\KemkesReport\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;

use Modules\GeneralBed\Models\Bed;
use Modules\PendaftaranVisit\Models\Visit;
use Modules\PendaftaranVisit\Models\VisitTransfer;

/**
 * RL SIRS lokal — port tiga rutin informasi Kemkes simgos2:
 *
 * - bedOccupancy          <- informasi.executeBedMonitorKemkes (+ infoRuangKamarTidur):
 *                            okupansi per bangsal/kelas dari kunjungan yang menginap,
 *                            BUKAN status live bed, agar laporan historis tetap valid.
 * - inpatientIndicators   <- informasi.executeIndikatorRS: indikator harian rawat inap
 *                            (pasien awal/masuk/pindahan/dipindahkan/mati<48j/>=48j/LD/
 *                            sisa/hari perawatan) plus rasio BOR/TOI/AVLOS/BTO/GDR/NDR.
 * - inpatientVisitsByClass<- informasi.listKunjunganRIKemkes: rekap kunjungan RI per kelas.
 *
 * Penyederhanaan dinyatakan vs simgos2:
 * - PINDAHAN (dari RS lain) = kunjungan yang registrasinya menunjuk Referral
 *   direction=incoming (Registration::referral()). Semantik legacy sedikit
 *   lebih luas (kunjungan.REF simgos2 juga mencakup mutasi/konsul intra-RS),
 *   tapi mutasi intra-RS di sini sudah tercakup terpisah di kolom DIPINDAHKAN
 *   (VisitTransfer) — interpretasi sempit "rujukan masuk dari RS lain" lebih
 *   sesuai nama field dan tidak dobel-hitung dengan DIPINDAHKAN.
 * - LD dihitung di PHP (DATEDIFF MySQL tidak tersedia di driver sqlite test suite).
 * - kemkes.statistikPasien (ODP/PDP/OTG COVID) tidak diport — konteks pandemi.
 */
class KemkesReportService
{
    /** Padan JENIS_KELAMIN 1/2 executeBedMonitorKemkes — dicocokkan via kode/nama, bukan id. */
    private const MALE_CODES = ['l', 'm', 'male', 'laki-laki'];

    private const FEMALE_CODES = ['p', 'f', 'female', 'perempuan'];

    /**
     * Okupansi tempat tidur per bangsal & kelas pada satu tanggal.
     *
     * @return array{date: string, rows: array<int, array<string, mixed>>, totals: array<string, mixed>}
     */
    public function bedOccupancy(?string $date = null): array
    {
        $day = CarbonImmutable::parse($date ?? now()->toDateString());
        $visits = $this->overnightVisits($day);

        // Kelompokkan kunjungan menginap per ward + kelas kamar tujuan.
        $perGroup = [];
        foreach ($visits as $visit) {
            $key = $visit->ward_id.'|'.$this->classIdOf($visit);
            $group = $perGroup[$key] ??= [
                'ward_id' => $visit->ward_id,
                'occupied' => 0,
                'male' => 0,
                'female' => 0,
            ];
            $group['occupied']++;
            [$male, $female] = $this->genderSplit($visit);
            $group['male'] += $male;
            $group['female'] += $female;
            $perGroup[$key] = $group;
        }

        // Master tempat tidur aktif per ward + kelas (padan COUNT(STATUS!=0)).
        $bedCounts = Bed::query()
            ->join('rooms', 'rooms.id', '=', 'beds.room_id')
            ->where('beds.is_active', true)
            ->where('beds.status', '!=', Bed::STATUS_MAINTENANCE)
            ->whereNotNull('rooms.ward_id')
            ->selectRaw('rooms.ward_id as ward_id, rooms.class_id as class_id, COUNT(*) as total')
            ->groupBy('rooms.ward_id', 'rooms.class_id')
            ->get();

        $rows = [];
        foreach ($bedCounts as $count) {
            $key = $count->ward_id.'|'.$count->class_id;
            $group = $perGroup[$key] ?? ['occupied' => 0, 'male' => 0, 'female' => 0];
            $total = (int) $count->total;

            $rows[] = [
                'ward_id' => (int) $count->ward_id,
                'class_id' => $count->class_id !== null ? (int) $count->class_id : null,
                'total_beds' => $total,
                'occupied' => (int) $group['occupied'],
                'available' => max(0, $total - (int) $group['occupied']),
                'male_patients' => (int) $group['male'],
                'female_patients' => (int) $group['female'],
                'occupancy_rate' => $total > 0 ? round($group['occupied'] / $total * 100, 1) : 0.0,
            ];
        }

        usort($rows, fn ($a, $b) => [$a['ward_id'], $a['class_id'] ?? -1] <=> [$b['ward_id'], $b['class_id'] ?? -1]);

        return [
            'date' => $day->toDateString(),
            'rows' => $rows,
            'totals' => [
                'total_beds' => array_sum(array_column($rows, 'total_beds')),
                'occupied' => array_sum(array_column($rows, 'occupied')),
                'available' => array_sum(array_column($rows, 'available')),
                'occupancy_rate' => $this->ratio(
                    array_sum(array_column($rows, 'occupied')),
                    array_sum(array_column($rows, 'total_beds'))
                ) * 100.0,
            ],
        ];
    }

    /**
     * Indikator harian rawat inap untuk rentang tanggal + agregat/rasio ala RL 4.1.
     *
     * @return array{from: string, to: string, days: array<int, array<string, int|null>>, summary: array<string, float|int|null>}
     */
    public function inpatientIndicators(string $from, ?string $to = null): array
    {
        $start = CarbonImmutable::parse($from)->startOfDay();
        $end = CarbonImmutable::parse($to ?? $from)->startOfDay();
        abort_if($end->lt($start), 422, 'Tanggal akhir mendahului tanggal awal.');

        $days = [];
        $sum = ['awal' => 0, 'masuk' => 0, 'pindahan' => 0, 'dipindahkan' => 0,
            'mati_kurang_48jam' => 0, 'mati_lebih_48jam' => 0, 'keluar' => 0,
            'lama_dirawat' => 0, 'sisa' => 0, 'hari_perawatan' => 0];
        $activeBeds = $this->activeBedCount();

        for ($day = $start; $day->lte($end); $day = $day->addDay()) {
            $dayStart = $day->startOfDay();
            $nextStart = $day->addDay()->startOfDay();

            $row = [
                'date' => $day->toDateString(),
                'awal' => $this->overlapCount($dayStart),
                'masuk' => Visit::query()
                    ->whereNotNull('ward_id')
                    ->where('status', '!=', 'cancelled')
                    ->whereBetween('admitted_at', [$dayStart, $nextStart])
                    ->count(),
                'pindahan' => Visit::query()
                    ->whereNotNull('ward_id')
                    ->where('status', '!=', 'cancelled')
                    ->whereBetween('admitted_at', [$dayStart, $nextStart])
                    ->whereHas('registration.referral', fn ($q) => $q->where('direction', 'incoming'))
                    ->count(),
                'dipindahkan' => VisitTransfer::query()
                    ->whereBetween('transferred_at', [$dayStart, $nextStart])
                    ->count(),
                'keluar' => 0,
                'mati_kurang_48jam' => 0,
                'mati_lebih_48jam' => 0,
                'lama_dirawat' => 0,
                'sisa' => 0,
                'hari_perawatan' => 0,
            ];

            // Pulang hari ini: jumlah keluar dan lama dirawat (PHP, portabel antar driver).
            $dischargedToday = Visit::query()
                ->whereNotNull('ward_id')
                ->where('status', '!=', 'cancelled')
                ->whereBetween('discharged_at', [$dayStart, $nextStart])
                ->get(['admitted_at', 'discharged_at']);
            $row['keluar'] = $dischargedToday->count();
            foreach ($dischargedToday as $visit) {
                // Padan MySQL DATEDIFF(keluar, masuk) — selisih tanggal penuh.
                $row['lama_dirawat'] += (int) $visit->admitted_at->startOfDay()
                    ->diffInDays($visit->discharged_at->startOfDay());
            }

            foreach ($this->deathsOn($day) as $death) {
                if ($death['hours'] < 48) {
                    $row['mati_kurang_48jam']++;
                } else {
                    $row['mati_lebih_48jam']++;
                }
            }

            // Sisa & hari perawatan: masih menginap malam ini (padan union terakhir SP).
            $row['sisa'] = $this->overnightQuery($day)->count();
            $row['hari_perawatan'] = $row['sisa'];

            foreach ($sum as $k => $v) {
                $sum[$k] = $v + $row[$k];
            }

            $days[] = $row;
        }

        $periodDays = max(1, $start->diffInDays($end) + 1);
        $bedDays = $activeBeds * $periodDays;
        $diedTotal = $sum['mati_kurang_48jam'] + $sum['mati_lebih_48jam'];

        return [
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'days' => $days,
            'summary' => [
                'total_beds' => $activeBeds,
                'bor_percent' => $this->maybeRatio($sum['lama_dirawat'], $bedDays, 100.0),
                'avlos_days' => $this->maybeRatio($sum['lama_dirawat'], $sum['keluar']),
                'toi_days' => $sum['keluar'] > 0
                    ? round(max(0, $bedDays - $sum['lama_dirawat']) / $sum['keluar'], 2)
                    : null,
                'bto_times' => $activeBeds > 0 && $bedDays > 0
                    ? round($sum['keluar'] / max(1, $activeBeds - $sum['lama_dirawat'] / $periodDays), 2)
                    : null,
                'gdr_per_mille' => $this->maybeRatio($diedTotal, $sum['keluar'], 1000.0),
                'ndr_per_mille' => $this->maybeRatio($sum['mati_lebih_48jam'], $sum['lama_dirawat'], 1000.0),
            ] + $sum,
        ];
    }

    /**
     * Rekap kunjungan rawat inap per kelas kamar (padan listKunjunganRIKemkes).
     *
     * @return array{from: string, to: string, rows: array<int, array<string, mixed>>}
     */
    public function inpatientVisitsByClass(string $from, ?string $to = null): array
    {
        $start = CarbonImmutable::parse($from)->startOfDay();
        $end = CarbonImmutable::parse($to ?? $from)->endOfDay();

        $visits = Visit::query()
            ->whereNotNull('ward_id')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('admitted_at', [$start, $end])
            ->with(['bed.room.roomClass'])
            ->get();

        $rows = [];
        foreach ($visits as $visit) {
            $className = $visit->bed?->room?->roomClass?->name ?? 'Tanpa Kelas';
            $rows[$className] ??= ['class_name' => $className, 'visits' => 0];
            $rows[$className]['visits']++;
        }

        ksort($rows);

        return ['from' => $start->toDateString(), 'to' => $end->toDateString(), 'rows' => array_values($rows)];
    }

    /** Kunjungan RI yang menginap pada $day beserta relasi gender. */
    private function overnightVisits(CarbonImmutable $day): \Illuminate\Database\Eloquent\Collection
    {
        return $this->overnightQuery($day)
            ->with(['registration.patient:id,gender_id', 'registration.patient.gender:id,name,code', 'bed.room:id,class_id'])
            ->get();
    }

    private function overnightQuery(CarbonImmutable $day): \Illuminate\Database\Eloquent\Builder
    {
        // Masuk sebelum hari berakhir DAN belum pulang saat hari itu usai (batas eksklusif).
        return Visit::query()
            ->whereNotNull('ward_id')
            ->where('status', '!=', 'cancelled')
            ->where('admitted_at', '<', $day->addDay()->startOfDay())
            ->where(function ($q) use ($day): void {
                $q->whereNull('discharged_at')->orWhere('discharged_at', '>=', $day->addDay()->startOfDay());
            });
    }

    /** Pasien awal hari: sudah masuk sebelum awal hari dan belum pulang saat itu. */
    private function overlapCount(CarbonImmutable $dayStart): int
    {
        return Visit::query()
            ->whereNotNull('ward_id')
            ->where('status', '!=', 'cancelled')
            ->where('admitted_at', '<', $dayStart)
            ->where(function ($q) use ($dayStart): void {
                $q->whereNull('discharged_at')->orWhere('discharged_at', '>=', $dayStart);
            })
            ->count();
    }

    /** Kematian hari itu dari rekam pulang, dengan lama rawat jam. */
    private function deathsOn(CarbonImmutable $day): array
    {
        return \Modules\LayananPatientDischargeRecord\Models\PatientDischargeRecord::query()
            ->where('discharge_method', 'died')
            ->whereBetween('patient_discharge_records.discharged_at', [$day->startOfDay(), $day->addDay()->startOfDay()])
            ->join('visits', 'visits.id', '=', 'patient_discharge_records.visit_id')
            ->get(['patient_discharge_records.discharged_at', 'visits.admitted_at'])
            // Hasil join bukan model ter-cast — parse eksplisit.
            ->map(fn ($row) => [
                'hours' => Carbon::parse($row->admitted_at)->diffInHours(Carbon::parse($row->discharged_at)),
            ])
            ->all();
    }

    private function activeBedCount(): int
    {
        return (int) Bed::query()
            ->join('rooms', 'rooms.id', '=', 'beds.room_id')
            ->where('beds.is_active', true)
            ->where('beds.status', '!=', Bed::STATUS_MAINTENANCE)
            ->count();
    }

    private function classIdOf(Visit $visit): ?int
    {
        return $visit->bed?->room?->class_id !== null ? (int) $visit->bed->room->class_id : null;
    }

    /** @return array{0: int, 1: int} */
    private function genderSplit(Visit $visit): array
    {
        $gender = $visit->registration?->patient?->gender;
        if ($gender === null) {
            return [0, 0];
        }

        $keys = array_map(strtolower(...), array_filter([$gender->code, $gender->name]));
        if (array_intersect($keys, self::MALE_CODES) !== []) {
            return [1, 0];
        }
        if (array_intersect($keys, self::FEMALE_CODES) !== []) {
            return [0, 1];
        }

        return [0, 0];
    }

    private function ratio(float|int $num, float|int $den): float
    {
        return $den > 0 ? round($num / $den, 4) : 0.0;
    }

    private function maybeRatio(float|int $num, float|int $den, float $factor = 1.0): ?float
    {
        return $den > 0 ? round($num / $den * $factor, 2) : null;
    }
}
