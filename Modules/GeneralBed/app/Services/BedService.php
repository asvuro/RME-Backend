<?php

namespace Modules\GeneralBed\Services;

use App\Modules\Contracts\BedGate;
use Illuminate\Support\Facades\DB;
use Modules\GeneralBed\Models\Bed;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Port state machine master.ruang_kamar_tidur simgos2. Okupansi adalah urusan
 * bed: modul lain meminta lewat BedGate, tidak pernah menulis status langsung.
 */
class BedService implements BedGate
{
    public function occupy(int $bedId): void
    {
        DB::transaction(function () use ($bedId) {
            $bed = Bed::query()->lockForUpdate()->findOrFail($bedId);

            abort_if(! $bed->is_active, 422, "Bed #{$bedId} tidak aktif.");
            abort_if($bed->status === Bed::STATUS_MAINTENANCE, 422, "Bed #{$bedId} sedang perbaikan.");

            // Port trigger onAfterInsertKunjungan: bed harus kosong sebelum terisi.
            abort_if($bed->status !== Bed::STATUS_AVAILABLE,
                422,
                "Bed #{$bedId} sudah dipesan atau terisi.",
            );

            $bed->update(['status' => Bed::STATUS_OCCUPIED]);
        });
    }

    public function release(int $bedId): void
    {
        DB::transaction(function () use ($bedId) {
            $bed = Bed::query()->lockForUpdate()->find($bedId);

            if ($bed === null || $bed->status !== Bed::STATUS_OCCUPIED) {
                return; // idempoten: bed tak ada / belum terisi, tidak ada yang dibebaskan
            }

            // Port cek trigger onAfterUpdateKunjungan: jangan bebaskan bila masih
            // ada kunjungan aktif lain yang menunjuk bed ini.
            $masihDipakai = Visit::query()
                ->where('bed_id', $bedId)
                ->whereNull('discharged_at')
                ->where('status', '!=', 'cancelled')
                ->exists();

            abort_if($masihDipakai, 422, "Bed #{$bedId} masih dipakai kunjungan aktif lain.");

            $bed->update(['status' => Bed::STATUS_AVAILABLE]);
        });
    }

    public function setMaintenance(int $bedId, bool $on): void
    {
        DB::transaction(function () use ($bedId, $on) {
            $bed = Bed::query()->lockForUpdate()->findOrFail($bedId);

            if ($on) {
                abort_if($bed->status === Bed::STATUS_OCCUPIED, 422, "Bed #{$bedId} sedang terisi pasien.");
                $bed->update(['status' => Bed::STATUS_MAINTENANCE]);

                return;
            }

            // Keluar perbaikan hanya ke kosong bila memang sedang maintenance.
            abort_if($bed->status !== Bed::STATUS_MAINTENANCE, 422, "Bed #{$bedId} tidak sedang perbaikan.");
            $bed->update(['status' => Bed::STATUS_AVAILABLE]);
        });
    }
}
