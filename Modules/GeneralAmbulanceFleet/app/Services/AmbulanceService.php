<?php

namespace Modules\GeneralAmbulanceFleet\Services;

use Illuminate\Support\Facades\DB;
use Modules\GeneralAmbulanceFleet\Models\Ambulance;

/**
 * Master data armada + gerbang status manual. Invariant yang dijaga:
 * 'in_use' TIDAK PERNAH bisa di-set dari jalur ini - status itu hanya lahir
 * dari AmbulanceTripService::start() dan hanya hilang lewat complete().
 * Tanpa pembatasan ini petugas bisa "meminjam" ambulans tanpa trip sehingga
 * gerbang start()/complete() jadi bisa dilewati.
 */
class AmbulanceService
{
    /**
     * Daftarkan armada baru. Status selalu available apa pun isinya request:
     * in_use/maintenance harus berasal dari kejadian bisnis, bukan master data.
     *
     * @param  array<string, mixed>  $data  hasil validasi StoreAmbulanceRequest
     */
    public function register(array $data): Ambulance
    {
        return DB::transaction(function () use ($data) {
            return Ambulance::create([
                ...$data,
                'status' => Ambulance::STATUS_AVAILABLE,
            ]);
        });
    }

    /**
     * Ubah data armada. Transisi status manual hanya available <-> maintenance;
     * selama ambulans menjalankan trip (in_use), satu-satunya jalan keluar adalah
     * menyelesaikan trip lewat AmbulanceTripService::complete() supaya
     * returned_at ikut tercatat.
     *
     * @param  array<string, mixed>  $data  hasil validasi UpdateAmbulanceRequest
     */
    public function updateDetails(Ambulance $ambulance, array $data): Ambulance
    {
        return DB::transaction(function () use ($ambulance, $data) {
            $locked = Ambulance::query()->lockForUpdate()->findOrFail($ambulance->id);

            $nextStatus = $data['status'] ?? null;

            if ($nextStatus !== null && $nextStatus !== $locked->status) {
                abort_if(
                    $nextStatus === Ambulance::STATUS_IN_USE,
                    422,
                    'Status in_use tidak dapat diatur manual; buat trip untuk memakai ambulans.',
                );

                abort_if(
                    $locked->status === Ambulance::STATUS_IN_USE,
                    422,
                    "Ambulans {$locked->vehicle_code} sedang menjalankan trip; selesaikan trip agar kembali available.",
                );
            }

            $locked->update($data);

            return $locked;
        });
    }
}
