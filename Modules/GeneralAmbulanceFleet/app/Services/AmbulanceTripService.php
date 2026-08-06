<?php

namespace Modules\GeneralAmbulanceFleet\Services;

use Illuminate\Support\Facades\DB;
use Modules\GeneralAmbulanceFleet\Models\Ambulance;
use Modules\GeneralAmbulanceFleet\Models\AmbulanceTrip;

/**
 * Gerbang state machine ambulans <-> trip. Satu ambulans hanya boleh
 * menjalankan satu trip aktif pada satu waktu: start() menolak (422) bila
 * ambulans sedang in_use atau maintenance, lalu mengunci ambulans dan trip
 * ke status yang saling konsisten dalam satu transaksi. complete() adalah
 * satu-satunya jalan mengembalikan ambulans ke available - controller TIDAK
 * boleh menulis Ambulance::update()/AmbulanceTrip::update() langsung untuk
 * transisi status.
 */
class AmbulanceTripService
{
    /**
     * @param  array<string, mixed>  $data  hasil validasi StoreAmbulanceTripRequest
     */
    public function start(array $data): AmbulanceTrip
    {
        return DB::transaction(function () use ($data) {
            $ambulance = Ambulance::query()->lockForUpdate()->findOrFail($data['ambulance_id']);

            abort_if(
                $ambulance->status === Ambulance::STATUS_IN_USE,
                422,
                "Ambulans {$ambulance->vehicle_code} sedang dipakai trip lain.",
            );
            abort_if(
                $ambulance->status === Ambulance::STATUS_MAINTENANCE,
                422,
                "Ambulans {$ambulance->vehicle_code} sedang maintenance.",
            );

            $trip = AmbulanceTrip::create([
                ...$data,
                'departed_at' => $data['departed_at'] ?? now(),
                // status TIDAK boleh datang dari input: trip baru selalu mulai
                // 'ongoing' agar gerbang complete() tidak bisa dilewati sejak
                // awal (pola sama seperti Visit::admit()).
                'status' => AmbulanceTrip::STATUS_ONGOING,
            ]);

            $ambulance->update(['status' => Ambulance::STATUS_IN_USE]);

            return $trip;
        });
    }

    /**
     * Selesaikan trip: catat waktu kembali dan bebaskan ambulans ke available.
     * Ditolak bila trip sudah tidak berjalan (completed/cancelled) - idempoten
     * terhadap double-submit tidak diizinkan, harus eksplisit gagal 422.
     */
    public function complete(AmbulanceTrip $trip, ?string $returnedAt = null): AmbulanceTrip
    {
        return DB::transaction(function () use ($trip, $returnedAt) {
            $trip = AmbulanceTrip::query()->lockForUpdate()->findOrFail($trip->id);

            abort_if(
                $trip->status !== AmbulanceTrip::STATUS_ONGOING,
                422,
                "Trip #{$trip->id} sudah {$trip->status}, tidak bisa diselesaikan.",
            );

            $trip->update([
                'status' => AmbulanceTrip::STATUS_COMPLETED,
                'returned_at' => $returnedAt ?? now(),
            ]);

            $ambulance = Ambulance::query()->lockForUpdate()->find($trip->ambulance_id);
            if ($ambulance !== null) {
                $ambulance->update(['status' => Ambulance::STATUS_AVAILABLE]);
            }

            return $trip->refresh();
        });
    }

    /**
     * Koreksi data trip (rute, sopir, pasien, waktu berangkat) SELAMA trip
     * masih berjalan. Setelah completed/cancelled, record jadi jejak audit
     * dan tidak boleh diamendemen - kesalahan setelah selesai dicatat sebagai
     * trip baru, bukan suntingan silang.
     *
     * @param  array<string, mixed>  $data  hasil validasi UpdateAmbulanceTripRequest
     */
    public function updateDetails(AmbulanceTrip $trip, array $data): AmbulanceTrip
    {
        return DB::transaction(function () use ($trip, $data) {
            $locked = AmbulanceTrip::query()->lockForUpdate()->findOrFail($trip->id);

            abort_if(
                $locked->status !== AmbulanceTrip::STATUS_ONGOING,
                422,
                "Trip #{$locked->id} sudah {$locked->status}, tidak bisa diedit.",
            );

            $locked->update($data);

            return $locked;
        });
    }
}
