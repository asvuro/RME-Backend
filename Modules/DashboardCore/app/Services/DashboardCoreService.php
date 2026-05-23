<?php

namespace Modules\DashboardCore\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Modules\GeneralBed\Models\Bed;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;
use Modules\LayananPrescription\Models\Prescription;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranPayment\Models\Payment;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * KPI inti RS untuk satu tanggal — padan ringkas rutin informasi simgos2
 * (infoRuangKamarTidur utk okupansi live) plus arus kunjungan/tagihan/farmasi.
 */
class DashboardCoreService
{
    /**
     * @return array<string, mixed>
     */
    public function core(?string $date = null): array
    {
        $day = CarbonImmutable::parse($date ?? now()->toDateString());
        $dayStart = $day->startOfDay();
        $nextStart = $day->addDay()->startOfDay();

        return [
            'date' => $day->toDateString(),
            'occupancy' => $this->occupancy(),
            'inpatients_active' => Visit::query()
                ->whereNotNull('ward_id')
                ->where('status', '!=', 'cancelled')
                ->whereNull('discharged_at')
                ->count(),
            'admissions_today' => $this->admissionQuery($dayStart, $nextStart)->count(),
            'discharges_today' => Visit::query()
                ->whereNotNull('ward_id')
                ->where('status', '!=', 'cancelled')
                ->whereBetween('discharged_at', [$dayStart, $nextStart])
                ->count(),
            'invoices_today' => [
                'count' => Invoice::query()->whereBetween('invoice_date', [$dayStart, $nextStart])->count(),
                'total_amount' => (float) Invoice::query()
                    ->whereBetween('invoice_date', [$dayStart, $nextStart])
                    ->sum('total_amount'),
            ],
            'payments_today' => [
                'count' => Payment::query()->whereBetween('paid_at', [$dayStart, $nextStart])->count(),
                'total_amount' => (float) Payment::query()
                    ->whereBetween('paid_at', [$dayStart, $nextStart])
                    ->sum('amount'),
            ],
            'prescriptions_today' => [
                'created' => Prescription::query()->whereBetween('prescribed_at', [$dayStart, $nextStart])->count(),
                'dispensed' => PharmacyDispense::query()->whereBetween('dispensed_at', [$dayStart, $nextStart])->count(),
            ],
            'trend' => $this->trend($day),
        ];
    }

    /** Okupansi LIVE dari status bed (dashboard realtime; laporan historis ada di KemkesReport). */
    private function occupancy(): array
    {
        $counts = Bed::query()
            ->where('is_active', true)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $occupied = (int) ($counts[Bed::STATUS_OCCUPIED] ?? 0);
        $reserved = (int) ($counts[Bed::STATUS_RESERVED] ?? 0);
        $available = (int) ($counts[Bed::STATUS_AVAILABLE] ?? 0);
        $total = $occupied + $reserved + $available;

        return [
            'total_beds' => $total,
            'occupied' => $occupied,
            'reserved' => $reserved,
            'available' => $available,
            'maintenance' => (int) Bed::query()->where('is_active', true)->where('status', Bed::STATUS_MAINTENANCE)->count(),
            'occupancy_rate' => $total > 0 ? round($occupied / $total * 100, 1) : 0.0,
        ];
    }

    /**
     * Tren admit/pulang tujuh hari terakhir berakhir di tanggal acuan.
     *
     * @return array<int, array<string, mixed>>
     */
    private function trend(CarbonImmutable $anchor): array
    {
        $rows = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $anchor->subDays($i);
            $start = $day->startOfDay();
            $end = $day->addDay()->startOfDay();

            $rows[] = [
                'date' => $day->toDateString(),
                'admissions' => $this->admissionQuery($start, $end)->count(),
                'discharges' => Visit::query()
                    ->whereNotNull('ward_id')
                    ->where('status', '!=', 'cancelled')
                    ->whereBetween('discharged_at', [$start, $end])
                    ->count(),
            ];
        }

        return $rows;
    }

    private function admissionQuery(CarbonImmutable $start, CarbonImmutable $end): \Illuminate\Database\Eloquent\Builder
    {
        return Visit::query()
            ->whereNotNull('ward_id')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('admitted_at', [$start, $end]);
    }
}
