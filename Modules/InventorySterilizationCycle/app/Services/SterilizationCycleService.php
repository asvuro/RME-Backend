<?php

namespace Modules\InventorySterilizationCycle\Services;

use Illuminate\Support\Facades\DB;
use Modules\InventorySterilizationCycle\Models\SterilizationCycle;

/**
 * Siklus CSSD. cycle_number adalah kalkulasi (bukan input pengguna) sehingga
 * pembuatannya lewat service, bukan Model::create() langsung di controller.
 */
class SterilizationCycleService
{
    public function createCycle(array $data): SterilizationCycle
    {
        return DB::transaction(function () use ($data) {
            $data['cycle_number'] = SterilizationCycle::generateCycleNumber();
            $data['biological_indicator_result'] ??= SterilizationCycle::BI_PENDING;
            $data['status'] ??= SterilizationCycle::STATUS_IN_PROCESS;

            return SterilizationCycle::create($data);
        });
    }

    public function updateCycle(SterilizationCycle $cycle, array $data): SterilizationCycle
    {
        $cycle->update($data);

        return $cycle->fresh();
    }

    /**
     * Hapus cycle. Ditolak 422 bila sudah punya SterilizedItem turunan —
     * mencegah item steril yatim yang cycle_id-nya sudah tak ada.
     */
    public function deleteCycle(SterilizationCycle $cycle): void
    {
        DB::transaction(function () use ($cycle) {
            $cycle = SterilizationCycle::query()->lockForUpdate()->findOrFail($cycle->id);

            abort_if(
                $cycle->sterilizedItems()->exists(),
                422,
                "Cycle {$cycle->cycle_number} sudah punya item steril turunan, tidak bisa dihapus.",
            );

            $cycle->delete();
        });
    }
}
