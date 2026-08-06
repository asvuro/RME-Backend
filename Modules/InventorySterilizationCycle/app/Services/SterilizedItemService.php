<?php

namespace Modules\InventorySterilizationCycle\Services;

use App\Modules\Contracts\HospitalConfig;
use Illuminate\Support\Facades\DB;
use Modules\InventorySterilizationCycle\Models\SterilizationCycle;
use Modules\InventorySterilizationCycle\Models\SterilizedItem;

/**
 * Gerbang inti modul: item steril hanya boleh lahir dari cycle yang SUDAH
 * lulus (status=passed) DAN indikator biologisnya negatif — CSSD tidak boleh
 * meloloskan barang dari siklus yang gagal/masih diproses/BI belum negatif,
 * berapa pun tekanan/suhunya. expiry_date = completed_at cycle + shelf life
 * (config 'cssd.default_shelf_life_days', default 30 hari bila belum diisi).
 */
class SterilizedItemService
{
    public function __construct(protected HospitalConfig $config) {}

    public function createItem(int $cycleId, array $data): SterilizedItem
    {
        return DB::transaction(function () use ($cycleId, $data) {
            $cycle = SterilizationCycle::query()->lockForUpdate()->findOrFail($cycleId);

            abort_if(
                $cycle->status !== SterilizationCycle::STATUS_PASSED,
                422,
                "Cycle {$cycle->cycle_number} belum lulus (status: {$cycle->status}), item steril tidak boleh dibuat.",
            );

            abort_if(
                $cycle->biological_indicator_result !== SterilizationCycle::BI_NEGATIVE,
                422,
                "Indikator biologis cycle {$cycle->cycle_number} belum negatif, item steril tidak boleh dibuat.",
            );

            // Prasyarat perhitungan expiry_date — seharusnya selalu terisi
            // begitu status=passed, tapi dijaga eksplisit agar tidak crash.
            abort_if(
                $cycle->completed_at === null,
                422,
                "Cycle {$cycle->cycle_number} belum punya completed_at, expiry item tidak bisa dihitung.",
            );

            $shelfLifeDays = (int) $this->config->get('cssd.default_shelf_life_days', 30);

            return SterilizedItem::create([
                'cycle_id' => $cycle->id,
                'item_name' => $data['item_name'],
                'quantity' => $data['quantity'],
                'expiry_date' => $cycle->completed_at->copy()->addDays($shelfLifeDays),
            ]);
        });
    }

    public function updateItem(SterilizedItem $item, array $data): SterilizedItem
    {
        $item->update($data);

        return $item->fresh();
    }

    public function checkExpiry(SterilizedItem $item): bool
    {
        return $item->expiry_date->isPast();
    }
}
