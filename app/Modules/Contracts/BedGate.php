<?php

namespace App\Modules\Contracts;

/**
 * Kontrak state machine bed lintas modul.
 *
 * Ala simgos2: bed adalah sumber kebenaran okupansi — insert kunjungan ber-bed
 * menandai terisi (trigger onAfterInsertKunjungan), kunjungan pulang/batal
 * membebaskan dengan cek tidak ada kunjungan aktif lain yang masih menunjuk
 * bed itu (trigger onAfterUpdateKunjungan).
 *
 * Implementasi: Modules\GeneralBed\Services\BedService.
 */
interface BedGate
{
    /**
     * Tandai bed terisi (STATUS=3). Tolak 422 bila bed nonaktif/maintenance
     * atau belum kosong.
     */
    public function occupy(int $bedId): void;

    /**
     * Bebaskan bed (STATUS=1). Hanya efektif bila tidak ada kunjungan aktif
     * lain yang masih menunjuk bed tersebut; idempoten untuk bed yang sudah
     * kosong.
     */
    public function release(int $bedId): void;

    /**
     * Masuk/keluar mode perbaikan. Tolak 422 bila bed sedang terisi dan
     * ingin masuk perbaikan.
     */
    public function setMaintenance(int $bedId, bool $on): void;

    /**
     * Pesan bed (STATUS=reserved) untuk admission yang direncanakan. Tolak
     * 422 bila bed nonaktif/maintenance/sudah dipesan/terisi. Expiry diambil
     * dari HospitalConfig key bed.reservation_ttl_minutes (default 60 menit).
     */
    public function reserve(int $bedId): void;

    /**
     * Bebaskan reservasi (STATUS=reserved -> available). Idempoten bila bed
     * tidak sedang reserved. $auto=true dipakai oleh sapuan expiry otomatis
     * (bed:release-expired-reservations) — hanya efektif bila reserved_until
     * sudah lewat; $auto=false (rilis manual) selalu diizinkan selama masih
     * reserved.
     */
    public function releaseReservation(int $bedId, bool $auto = false): void;
}
