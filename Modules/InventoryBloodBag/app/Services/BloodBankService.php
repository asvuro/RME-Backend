<?php

namespace Modules\InventoryBloodBag\Services;

use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\InventoryBloodBag\Models\BloodBag;
use Modules\InventoryBloodBag\Models\CrossmatchTest;

/**
 * Gerbang state machine kantong darah BDRS (Bank Darah RS). Semua perubahan
 * status BloodBag dan pembuatan CrossmatchTest WAJIB lewat service ini —
 * controller tidak boleh menulis Model::create()/update() langsung untuk
 * aksi yang punya gerbang bisnis.
 */
class BloodBankService
{
    /**
     * Jalankan uji crossmatch untuk sebuah kantong darah. Hanya kantong
     * berstatus in_stock yang boleh diuji (kantong yang sudah direservasi,
     * ditransfusikan, dsb tidak boleh diuji ulang lewat jalur ini). Bila
     * hasil kompatibel (mayor+minor+auto control semuanya negatif), kantong
     * otomatis direservasi selama 48 jam lewat reserveForCrossmatch().
     * Bila tidak kompatibel, hasil tetap dicatat tapi kantong TETAP in_stock
     * (tidak layak dipakai untuk pasien ini, tapi masih bisa diuji untuk
     * pasien lain).
     *
     * Asumsi: tested_by boleh dikirim eksplisit lewat payload; kalau kosong,
     * dipakai employee yang tertaut ke user yang sedang login (petugas lab).
     * Kalau user tidak punya employee tertaut, dibiarkan null — bukan error,
     * karena pencatatan penguji bersifat pelengkap audit.
     */
    public function performCrossmatch(int $bloodBagId, array $data, ?User $user = null): CrossmatchTest
    {
        return DB::transaction(function () use ($bloodBagId, $data, $user) {
            $bag = BloodBag::query()->lockForUpdate()->findOrFail($bloodBagId);

            abort_if(
                $bag->status !== BloodBag::STATUS_IN_STOCK,
                422,
                "Kantong darah #{$bloodBagId} berstatus {$bag->status}, hanya kantong in_stock yang bisa diuji crossmatch.",
            );

            $testedAt = now();
            $isCompatible = CrossmatchTest::computeIsCompatible(
                $data['major_result'],
                $data['minor_result'],
                $data['auto_control'],
            );
            $reservedUntil = $testedAt->copy()->addHours(48);

            $test = CrossmatchTest::create([
                'blood_bag_id' => $bag->id,
                'patient_id' => $data['patient_id'],
                'major_result' => $data['major_result'],
                'minor_result' => $data['minor_result'],
                'auto_control' => $data['auto_control'],
                'is_compatible' => $isCompatible,
                'tested_by' => $data['tested_by'] ?? $this->employeeIdFor($user),
                'tested_at' => $testedAt,
                'reserved_until' => $reservedUntil,
            ]);

            if ($isCompatible) {
                $this->reserveForCrossmatch($bag);
            }

            return $test;
        });
    }

    /**
     * Gerbang reservasi: cuma boleh reserve kantong berstatus in_stock.
     * Dipanggil dari performCrossmatch() saat hasil kompatibel.
     *
     * Catatan desain: reserved_until TIDAK disimpan di tabel blood_bags —
     * satu-satunya sumber kebenaran adalah kolom reserved_until di baris
     * crossmatch_tests yang memicu reservasi ini (tested_at + 48 jam), supaya
     * tidak ada dua nilai masa berlaku yang bisa saling bertentangan.
     */
    public function reserveForCrossmatch(BloodBag $bag): BloodBag
    {
        abort_if(
            $bag->status !== BloodBag::STATUS_IN_STOCK,
            422,
            "Kantong darah #{$bag->id} berstatus {$bag->status}, tidak bisa direservasi.",
        );

        $bag->update(['status' => BloodBag::STATUS_CROSSMATCH_RESERVED]);

        return $bag->fresh();
    }

    /**
     * Lepas reservasi crossmatch sebuah kantong lewat hasil uji terkait.
     * Dua jalur pemanggilan:
     *  - manual (staff membatalkan rencana transfusi sebelum reserved_until
     *    lewat) -> selalu diizinkan selama kantong masih crossmatch_reserved.
     *  - auto (dipanggil oleh proses housekeeping/expiry) -> hanya diizinkan
     *    bila reserved_until sudah lewat, gerbang $auto=true menolak 422
     *    kalau reservasi belum expired supaya tidak melepas reservasi yang
     *    masih berlaku secara tidak sengaja.
     */
    public function release(int $crossmatchTestId, bool $auto = false): CrossmatchTest
    {
        return DB::transaction(function () use ($crossmatchTestId, $auto) {
            $test = CrossmatchTest::query()->lockForUpdate()->findOrFail($crossmatchTestId);
            $bag = BloodBag::query()->lockForUpdate()->findOrFail($test->blood_bag_id);

            abort_if(
                $bag->status !== BloodBag::STATUS_CROSSMATCH_RESERVED,
                422,
                "Kantong darah #{$bag->id} berstatus {$bag->status}, tidak ada reservasi untuk dilepas.",
            );

            if ($auto) {
                abort_if(
                    $test->reserved_until !== null && now()->lt($test->reserved_until),
                    422,
                    "Reservasi kantong darah #{$bag->id} belum expired.",
                );
            }

            $bag->update(['status' => BloodBag::STATUS_IN_STOCK]);

            return $test->fresh();
        });
    }

    /**
     * Tandai kantong sebagai sudah ditransfusikan. Gerbang: kantong harus
     * sudah melalui crossmatch (status crossmatch_reserved) — mencegah
     * transfusi kantong yang belum diuji kompatibilitasnya.
     */
    public function markTransfused(int $bloodBagId): BloodBag
    {
        return DB::transaction(function () use ($bloodBagId) {
            $bag = BloodBag::query()->lockForUpdate()->findOrFail($bloodBagId);

            abort_if(
                $bag->status !== BloodBag::STATUS_CROSSMATCH_RESERVED,
                422,
                "Kantong darah #{$bloodBagId} berstatus {$bag->status}, harus direservasi via crossmatch dulu sebelum ditransfusikan.",
            );

            $bag->update(['status' => BloodBag::STATUS_TRANSFUSED]);

            return $bag->fresh();
        });
    }

    /**
     * Cari employee id yang tertaut ke user (untuk default tested_by).
     * Null-safe: user null / tanpa employee tertaut mengembalikan null.
     */
    protected function employeeIdFor(?User $user): ?int
    {
        if ($user === null) {
            return null;
        }

        return Employee::query()->where('user_id', $user->id)->value('id');
    }
}
