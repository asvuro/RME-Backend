<?php

namespace Modules\GeneralBed\Services;

use App\Modules\Contracts\BedGate;
use App\Modules\Contracts\HospitalConfig;
use App\Modules\Contracts\WardScope;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Port state machine master.ruang_kamar_tidur simgos2. Okupansi adalah urusan
 * bed: modul lain meminta lewat BedGate, tidak pernah menulis status langsung.
 */
class BedService implements BedGate
{
    public function __construct(protected WardScope $wardScope, protected HospitalConfig $config) {}

    public function occupy(int $bedId): void
    {
        DB::transaction(function () use ($bedId) {
            $bed = Bed::query()->lockForUpdate()->findOrFail($bedId);

            abort_if(! $bed->is_active, 422, "Bed #{$bedId} tidak aktif.");
            abort_if($bed->status === Bed::STATUS_MAINTENANCE, 422, "Bed #{$bedId} sedang perbaikan.");

            // Bed boleh terisi dari kosong ATAU dari reservasi sendiri (admission
            // yang sudah dipesan duluan) — bukan status lain (mis. sudah terisi).
            abort_if(
                ! in_array($bed->status, [Bed::STATUS_AVAILABLE, Bed::STATUS_RESERVED], true),
                422,
                "Bed #{$bedId} sudah dipesan atau terisi.",
            );

            $bed->update(['status' => Bed::STATUS_OCCUPIED, 'reserved_until' => null]);
        });
    }

    public function reserve(int $bedId): void
    {
        DB::transaction(function () use ($bedId) {
            $bed = Bed::query()->lockForUpdate()->findOrFail($bedId);

            abort_if(! $bed->is_active, 422, "Bed #{$bedId} tidak aktif.");
            abort_if($bed->status === Bed::STATUS_MAINTENANCE, 422, "Bed #{$bedId} sedang perbaikan.");
            abort_if($bed->status !== Bed::STATUS_AVAILABLE, 422, "Bed #{$bedId} sudah dipesan atau terisi.");

            $ttlMinutes = (int) $this->config->get('bed.reservation_ttl_minutes', 60);

            $bed->update([
                'status' => Bed::STATUS_RESERVED,
                'reserved_until' => now()->addMinutes($ttlMinutes),
            ]);
        });
    }

    public function releaseReservation(int $bedId, bool $auto = false): void
    {
        DB::transaction(function () use ($bedId, $auto) {
            $bed = Bed::query()->lockForUpdate()->find($bedId);

            if ($bed === null || $bed->status !== Bed::STATUS_RESERVED) {
                return; // idempoten: bed tak ada / tidak sedang reserved
            }

            if ($auto) {
                // Sapuan otomatis hanya boleh melepas reservasi yang benar-benar
                // sudah lewat TTL-nya -- bukan menutup reservasi yang masih valid.
                abort_if(
                    $bed->reserved_until === null || now()->lt($bed->reserved_until),
                    422,
                    "Reservasi bed #{$bedId} belum kedaluwarsa.",
                );
            }

            $bed->update(['status' => Bed::STATUS_AVAILABLE, 'reserved_until' => null]);
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

    /**
     * Ubah atribut non-status bed (bed_number, is_active). Menonaktifkan bed
     * yang sedang dipesan/terisi ditolak 422 — status hanya boleh berubah
     * lewat occupy()/release()/setMaintenance(), dan gerbang is_active yang
     * dipakai occupy() tidak boleh dilewati untuk bed yang masih dipakai.
     */
    public function updateDetails(int $bedId, array $data, User $user): Bed
    {
        return DB::transaction(function () use ($bedId, $data, $user) {
            $bed = Bed::query()->lockForUpdate()->with('room')->findOrFail($bedId);

            abort_if(
                ! $this->wardScope->canAccessWard($user, $bed->room?->ward_id),
                403,
                "Anda tidak ditugaskan ke ward bed #{$bedId}.",
            );

            if (array_key_exists('is_active', $data) && ! $data['is_active']) {
                abort_if(
                    in_array($bed->status, [Bed::STATUS_OCCUPIED, Bed::STATUS_RESERVED], true),
                    422,
                    "Bed #{$bedId} masih {$bed->status}, tidak bisa dinonaktifkan.",
                );
            }

            $bed->update($data);

            return $bed;
        });
    }

    /**
     * Hapus bed. Ditolak 422 bila bed sedang dipesan/terisi, atau bila masih
     * ada kunjungan aktif yang menunjuk bed ini (sama seperti gerbang di
     * release()) — mencegah Visit yatim yang bed_id-nya sudah tak ada.
     */
    public function deleteBed(int $bedId, User $user): void
    {
        DB::transaction(function () use ($bedId, $user) {
            $bed = Bed::query()->lockForUpdate()->with('room')->findOrFail($bedId);

            abort_if(
                ! $this->wardScope->canAccessWard($user, $bed->room?->ward_id),
                403,
                "Anda tidak ditugaskan ke ward bed #{$bedId}.",
            );

            abort_if(
                in_array($bed->status, [Bed::STATUS_OCCUPIED, Bed::STATUS_RESERVED], true),
                422,
                "Bed #{$bedId} masih {$bed->status}, tidak bisa dihapus.",
            );

            $masihDipakai = Visit::query()
                ->where('bed_id', $bedId)
                ->whereNull('discharged_at')
                ->where('status', '!=', 'cancelled')
                ->exists();

            abort_if($masihDipakai, 422, "Bed #{$bedId} masih dipakai kunjungan aktif lain.");

            $bed->delete();
        });
    }

    /**
     * Sapuan housekeeping: lepas semua reservasi yang sudah lewat reserved_until.
     * Dipanggil oleh command bed:release-expired-reservations (cron/k8s eksternal
     * -- repo ini tidak memakai Laravel Scheduler).
     */
    public function releaseExpiredReservations(): int
    {
        $expiredIds = Bed::query()
            ->where('status', Bed::STATUS_RESERVED)
            ->whereNotNull('reserved_until')
            ->where('reserved_until', '<=', now())
            ->pluck('id');

        foreach ($expiredIds as $bedId) {
            $this->releaseReservation($bedId, auto: true);
        }

        return $expiredIds->count();
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
