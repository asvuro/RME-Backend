<?php

namespace Modules\LayananMedicineDelivery\Services;

use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananMedicineDelivery\Models\MedicineDelivery;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;

/**
 * Gerbang pengantaran obat oleh kurir.
 *
 * Mesin status sengaja sederhana - hanya ada dua endpoint tulis khusus:
 *
 *   pending --assign-courier--> dikirim --mark-delivered--> diterima
 *                                                  \------> gagal (dicatat
 *                                            operasional, tanpa endpoint)
 *
 * assign-courier sekaligus menjadi penanda "paket berangkat"
 * (pending -> dikirim) supaya alur lengkap tanpa perlu endpoint
 * mark-picked-up tambahan yang tidak diminta spesifikasi.
 */
class MedicineDeliveryService
{
    /**
     * Jadwalkan pengantaran untuk satu dispense farmasi.
     *
     * Status awal TIDAK diambil dari input: pengantaran selalu lahir 'pending'
     * tanpa kurir. requested_at boleh dijadwalkan klien, default sekarang.
     *
     * @param  array<string, mixed>  $data  hasil validasi StoreMedicineDeliveryRequest
     */
    public function create(array $data): MedicineDelivery
    {
        return DB::transaction(function () use ($data) {
            /** @var PharmacyDispense $dispense */
            $dispense = PharmacyDispense::query()->lockForUpdate()->findOrFail($data['pharmacy_dispense_id']);

            // Gerbang 1: hanya obat yang benar-benar sudah diserahkan farmasi
            // (status 'dispensed') punya barang fisik untuk dibawa kurir.
            // Dispense 'pending' belum jadi, 'cancelled' tidak jadi.
            abort_if(
                $dispense->status !== 'dispensed',
                422,
                "Obat belum diserahkan farmasi (status dispense: {$dispense->status}) - tidak bisa dibuatkan pengantaran.",
            );

            // Gerbang 2: satu dispense cuma boleh punya satu pengantaran -
            // mencegah dobel antrean kurir untuk obat yang sama.
            abort_if(
                MedicineDelivery::query()->where('pharmacy_dispense_id', $dispense->id)->exists(),
                422,
                'Dispense ini sudah terdaftar dalam satu pengantaran.',
            );

            return MedicineDelivery::create([
                'pharmacy_dispense_id' => $dispense->id,
                'patient_address' => $data['patient_address'],
                'courier_employee_id' => null,
                'status' => MedicineDelivery::STATUS_PENDING,
                'requested_at' => $data['requested_at'] ?? now(),
                'delivered_at' => null,
            ]);
        });
    }

    /**
     * Edit bebas hanya untuk alamat tujuan. Begitu pengantaran selesai
     * (diterima/gagal) alamat dikunci karena sudah menjadi jejak audit
     * ke mana obat pernah dibawa.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateAddress(MedicineDelivery $delivery, array $data): MedicineDelivery
    {
        abort_if(
            in_array($delivery->status, MedicineDelivery::CLOSED_STATUSES, true),
            422,
            "Pengantaran sudah selesai ({$delivery->status}) - alamat tidak bisa diubah lagi.",
        );

        $delivery->update($data);

        return $delivery->refresh();
    }

    /**
     * Tugaskan kurir untuk satu pengantaran; sekalian menandai paket
     * berangkat (pending -> dikirim). Re-assign saat masih 'dikirim' sah
     * (kurir berganti), tapi setelah diterima/gagal tidak bisa lagi.
     */
    public function assignCourier(MedicineDelivery $delivery, int $employeeId): MedicineDelivery
    {
        return DB::transaction(function () use ($employeeId, $delivery) {
            /** @var MedicineDelivery $delivery */
            $delivery = MedicineDelivery::query()->lockForUpdate()->findOrFail($delivery->id);

            abort_if(
                in_array($delivery->status, MedicineDelivery::CLOSED_STATUSES, true),
                422,
                "Pengantaran sudah selesai ({$delivery->status}) - kurir tidak bisa ditugaskan ulang.",
            );

            /** @var Employee $employee */
            $employee = Employee::query()->lockForUpdate()->findOrFail($employeeId);

            // Kurir nonaktif tidak boleh mendapat tugas antar.
            abort_if(
                ! $employee->is_active,
                422,
                "Karyawan #{$employeeId} tidak aktif - tidak bisa ditugaskan sebagai kurir.",
            );

            $delivery->update([
                'courier_employee_id' => $employee->id,
                'status' => MedicineDelivery::STATUS_DIKIRIM,
            ]);

            return $delivery->refresh();
        });
    }

    /**
     * Tandai pengantaran diterima pasien: wajib punya kurir dan benar-benar
     * sedang dikirim (tidak boleh lompat pending -> diterima tanpa jejak
     * berangkat), lalu delivered_at dicatat saat ini.
     */
    public function markDelivered(MedicineDelivery $delivery, User $user): MedicineDelivery
    {
        return DB::transaction(function () use ($user, $delivery) {
            /** @var MedicineDelivery $delivery */
            $delivery = MedicineDelivery::query()->lockForUpdate()->findOrFail($delivery->id);

            // Otorisasi lunak (403): bila kurir terhubung akun user, hanya dia
            // yang boleh menandai terkirim (yang mengantar yang melapor).
            // Employee tanpa akun dilewati karena tidak ada cara memverifikasi.
            $courierUserId = $delivery->courierEmployee?->user_id;
            abort_if(
                $courierUserId !== null && $courierUserId !== $user->id,
                403,
                'Hanya kurir yang ditugaskan yang bisa menandai pengantaran diterima.',
            );

            // Gerbang prasyarat (422): urutan cek dari yang paling dasar -
            // kurir ada, paket sudah berangkat, status belum tertutup.
            abort_if($delivery->courier_employee_id === null, 422, 'Pengantaran belum punya kurir - tugaskan lewat assign-courier dahulu.');

            abort_if(
                $delivery->status === MedicineDelivery::STATUS_PENDING,
                422,
                'Pengantaran belum berangkat (masih pending) - tidak bisa ditandai diterima.',
            );

            abort_if(
                $delivery->status === MedicineDelivery::STATUS_DITERIMA,
                422,
                'Pengantaran sudah ditandai diterima sebelumnya.',
            );

            abort_if(
                $delivery->status === MedicineDelivery::STATUS_GAGAL,
                422,
                'Pengantaran sudah dicatat gagal - buat pengantaran baru bila perlu dikirim ulang.',
            );

            $delivery->update([
                'status' => MedicineDelivery::STATUS_DITERIMA,
                'delivered_at' => now(),
            ]);

            return $delivery->refresh();
        });
    }

    /**
     * Hapus jadwal pengantaran yang belum berangkat. Riwayat yang sudah
     * jalan (dikirim/diterima/gagal) tidak boleh hilang dari jejak audit.
     */
    public function deleteDelivery(MedicineDelivery $delivery): void
    {
        abort_if(
            $delivery->status !== MedicineDelivery::STATUS_PENDING,
            422,
            "Hanya pengantaran berstatus pending yang bisa dihapus (status sekarang: {$delivery->status}).",
        );

        $delivery->delete();
    }
}
