<?php

namespace Modules\InventoryDietOrder\Services;

use Illuminate\Support\Facades\DB;
use Modules\InventoryDietOrder\Models\DietOrder;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Gerbang bisnis pesanan diet dapur gizi.
 *
 * State machine status (matriks transisi valid):
 *   ordered   -> prepared, cancelled
 *   prepared  -> delivered, cancelled
 *   delivered -> (terminal, tidak bisa berubah lagi)
 *   cancelled -> (terminal, tidak bisa berubah lagi)
 *
 * Alasan: dapur harus mengikuti alur linear pesan -> siapkan -> antar, dan
 * pembatalan hanya masuk akal sebelum makanan sampai ke pasien (delivered).
 * Controller TIDAK BOLEH menulis DietOrder::update() langsung untuk status —
 * hanya lewat transitionStatus() di sini.
 */
class DietOrderService
{
    /** @var array<string, array<int, string>> */
    private const ALLOWED_TRANSITIONS = [
        DietOrder::STATUS_ORDERED => [DietOrder::STATUS_PREPARED, DietOrder::STATUS_CANCELLED],
        DietOrder::STATUS_PREPARED => [DietOrder::STATUS_DELIVERED, DietOrder::STATUS_CANCELLED],
        DietOrder::STATUS_DELIVERED => [],
        DietOrder::STATUS_CANCELLED => [],
    ];

    /**
     * @param  array<string, mixed>  $data  hasil validasi StoreDietOrderRequest
     */
    public function create(array $data): DietOrder
    {
        return DB::transaction(function () use ($data) {
            // Gerbang: visit yang dituju harus benar-benar ada dan masih aktif
            // (belum pulang, belum dibatalkan) — dapur tidak boleh menyiapkan
            // diet untuk kunjungan yang sudah selesai.
            $visit = Visit::query()->lockForUpdate()->findOrFail($data['visit_id']);

            abort_if(
                $visit->discharged_at !== null || $visit->status === 'cancelled',
                422,
                'Kunjungan sudah pulang atau dibatalkan, tidak bisa membuat pesanan diet baru.',
            );

            $data['status'] = DietOrder::STATUS_ORDERED;

            return DietOrder::create($data);
        });
    }

    /**
     * @param  array<string, mixed>  $data  field non-status yang boleh diubah (masih di status ordered)
     */
    public function updateDetails(DietOrder $dietOrder, array $data): DietOrder
    {
        abort_if(
            $dietOrder->status !== DietOrder::STATUS_ORDERED,
            422,
            "Pesanan diet #{$dietOrder->id} sudah {$dietOrder->status}, detailnya tidak bisa diubah lagi.",
        );

        $dietOrder->update($data);

        return $dietOrder->fresh();
    }

    public function transitionStatus(DietOrder $dietOrder, string $newStatus): DietOrder
    {
        return DB::transaction(function () use ($dietOrder, $newStatus) {
            $locked = DietOrder::query()->lockForUpdate()->findOrFail($dietOrder->id);

            $allowed = self::ALLOWED_TRANSITIONS[$locked->status] ?? [];

            abort_if(
                ! in_array($newStatus, $allowed, true),
                422,
                "Status pesanan diet tidak bisa pindah dari {$locked->status} ke {$newStatus}.",
            );

            $locked->update(['status' => $newStatus]);

            return $locked;
        });
    }

    public function delete(DietOrder $dietOrder): void
    {
        // Hanya pesanan yang belum diantar yang boleh dihapus — pesanan yang
        // sudah delivered adalah catatan riwayat, dan cancelled biarkan tetap
        // ada sebagai jejak audit.
        abort_if(
            in_array($dietOrder->status, [DietOrder::STATUS_DELIVERED, DietOrder::STATUS_CANCELLED], true),
            422,
            "Pesanan diet #{$dietOrder->id} sudah {$dietOrder->status}, tidak bisa dihapus.",
        );

        $dietOrder->delete();
    }
}
