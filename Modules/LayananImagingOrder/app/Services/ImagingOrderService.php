<?php

namespace Modules\LayananImagingOrder\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\LayananImagingOrder\Models\ImagingOrder;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * State machine order imaging: ordered → scheduled → completed, dengan
 * cancel sebagai cabang dari ordered/scheduled. Semua perubahan status WAJIB
 * lewat sini — controller tidak pernah menulis kolom status langsung.
 */
class ImagingOrderService
{
    /**
     * Order baru selalu lahir berstatus 'ordered'. Kolom status tidak pernah
     * diterima dari input: bila klien bisa menyuntik status=completed saat
     * create, seluruh gerbang schedule/cancel bisa dilewati sejak awal
     * (pola yang sama dengan VisitService::admit()).
     *
     * @param  array<string, mixed>  $data  hasil validasi StoreImagingOrderRequest
     */
    public function create(array $data): ImagingOrder
    {
        return DB::transaction(function () use ($data) {
            $this->assertVisitOpen((int) $data['visit_id']);

            return ImagingOrder::create([
                ...Arr::except($data, ['status']),
                'status' => ImagingOrder::STATUS_ORDERED,
            ]);
        });
    }

    /**
     * Sunting atribut pemesanan (modality/body_part/ordered_by/ordered_at).
     * Ditolak setelah order completed/cancelled: hasil kerja dan pembatalan
     * adalah keputusan final — koreksi pasca-selesai harus lewat studi/amendemen,
     * bukan ubah diam-diam pesanannya.
     *
     * @param  array<string, mixed>  $data  hasil validasi UpdateImagingOrderRequest
     */
    public function updateDetails(ImagingOrder $order, array $data): ImagingOrder
    {
        return DB::transaction(function () use ($order, $data) {
            abort_if(
                in_array($order->status, [ImagingOrder::STATUS_COMPLETED, ImagingOrder::STATUS_CANCELLED], true),
                422,
                "Order imaging #{$order->id} sudah {$order->status}; tidak dapat disunting.",
            );

            $order->update($data);

            return $order->refresh();
        });
    }

    /**
     * Gerbang penjadwalan. Diizinkan dari 'ordered' maupun 'scheduled'
     * (penjadwalan ulang adalah operasi normal), ditolak dari
     * completed/cancelled karena keduanya sudah keluar dari antrean.
     */
    public function schedule(ImagingOrder $order, string $scheduledAt): ImagingOrder
    {
        abort_if(
            $order->status === ImagingOrder::STATUS_COMPLETED,
            422,
            "Order imaging #{$order->id} sudah selesai dikerjakan; tidak dapat dijadwalkan.",
        );
        abort_if(
            $order->status === ImagingOrder::STATUS_CANCELLED,
            422,
            "Order imaging #{$order->id} sudah dibatalkan; tidak dapat dijadwalkan.",
        );

        return DB::transaction(function () use ($order, $scheduledAt) {
            $order->update([
                'status' => ImagingOrder::STATUS_SCHEDULED,
                'scheduled_at' => $scheduledAt,
            ]);

            return $order->refresh();
        });
    }

    /**
     * Batalkan order (soft-cancel ala VisitService::cancel): baris tetap ada
     * untuk jejak audit, hanya statusnya berubah. Order yang sudah completed
     * tidak boleh dibatalkan — pengerjaan sudah terjadi dan tercatat di studi.
     */
    public function cancel(ImagingOrder $order): ImagingOrder
    {
        abort_if(
            $order->status === ImagingOrder::STATUS_COMPLETED,
            422,
            "Order imaging #{$order->id} sudah selesai dikerjakan; tidak dapat dibatalkan.",
        );
        abort_if(
            $order->status === ImagingOrder::STATUS_CANCELLED,
            422,
            "Order imaging #{$order->id} sudah dibatalkan.",
        );

        return DB::transaction(function () use ($order) {
            $order->update(['status' => ImagingOrder::STATUS_CANCELLED]);

            return $order->refresh();
        });
    }

    /**
     * Order hanya boleh dibuat untuk kunjungan yang masih aktif — port semangat
     * gerbang admit VisitService: kunjungan pulang/batal menutup seluruh
     * pemesanan layanan lanjutan di bawahnya.
     */
    protected function assertVisitOpen(int $visitId): void
    {
        $visit = Visit::query()->find($visitId);

        abort_if($visit === null, 422, "Kunjungan #{$visitId} tidak dikenal.");
        abort_if(
            $visit->discharged_at !== null || $visit->status === 'cancelled',
            422,
            "Kunjungan #{$visitId} sudah pulang/batal; order imaging tidak dapat dibuat.",
        );
    }
}
