<?php

namespace Modules\PegawaiRemunerasiJasaMedis\Services;

use Illuminate\Support\Facades\DB;
use Modules\PegawaiRemunerasiJasaMedis\Models\RemunerationEntry;

/**
 * Gerbang bisnis remunerasi jasa medis. net_amount TIDAK PERNAH diterima dari
 * request — selalu dihitung ulang di sini dari gross_amount, deduction_percentage,
 * dan fixed_deduction, supaya tidak ada celah client mengirim net_amount palsu
 * (pola sama seperti BedService: controller tidak boleh Model::create() langsung).
 */
class RemunerationService
{
    public function create(array $data): RemunerationEntry
    {
        return DB::transaction(function () use ($data) {
            $data['net_amount'] = $this->calculateNet($data);

            return RemunerationEntry::create($data);
        });
    }

    public function update(RemunerationEntry $entry, array $data): RemunerationEntry
    {
        return DB::transaction(function () use ($entry, $data) {
            $entry->lockForUpdate();

            $merged = array_merge([
                'gross_amount' => $entry->gross_amount,
                'deduction_percentage' => $entry->deduction_percentage,
                'fixed_deduction' => $entry->fixed_deduction,
            ], $data);

            $data['net_amount'] = $this->calculateNet($merged);

            $entry->update($data);

            return $entry->refresh();
        });
    }

    public function delete(RemunerationEntry $entry): void
    {
        $entry->delete();
    }

    /**
     * net = gross - (gross * deduction%) - potongan tetap opsional.
     * Gerbang: gross harus > 0, deduction_percentage 0-100, dan hasil net
     * tidak boleh negatif (potongan tetap yang terlalu besar ditolak,
     * bukan diam-diam menghasilkan angka minus di slip remunerasi).
     */
    public function calculateNet(array $data): float
    {
        $gross = (float) ($data['gross_amount'] ?? 0);
        $deductionPercentage = (float) ($data['deduction_percentage'] ?? 0);
        $fixedDeduction = (float) ($data['fixed_deduction'] ?? 0);

        abort_if($gross <= 0, 422, 'Gross amount harus lebih dari 0.');
        abort_if(
            $deductionPercentage < 0 || $deductionPercentage > 100,
            422,
            'Deduction percentage harus di antara 0 dan 100.',
        );
        abort_if($fixedDeduction < 0, 422, 'Fixed deduction tidak boleh negatif.');

        $net = $gross - ($gross * $deductionPercentage / 100) - $fixedDeduction;

        abort_if($net < 0, 422, 'Net amount hasil kalkulasi negatif — potongan melebihi gross amount.');

        return round($net, 2);
    }

    /**
     * Ringkasan total remunerasi per pegawai per periode (bulan/tahun),
     * difilter dari service_date (tanggal tindakan), bukan created_at.
     */
    public function summary(int $employeeId, int $month, int $year): array
    {
        $query = RemunerationEntry::query()
            ->where('employee_id', $employeeId)
            ->whereYear('service_date', $year)
            ->whereMonth('service_date', $month);

        return [
            'employee_id' => $employeeId,
            'month' => $month,
            'year' => $year,
            'entry_count' => (clone $query)->count(),
            'total_gross' => (float) (clone $query)->sum('gross_amount'),
            'total_net' => (float) (clone $query)->sum('net_amount'),
        ];
    }
}
