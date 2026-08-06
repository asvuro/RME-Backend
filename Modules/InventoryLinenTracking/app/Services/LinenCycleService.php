<?php

namespace Modules\InventoryLinenTracking\Services;

use Illuminate\Support\Facades\DB;
use Modules\InventoryLinenTracking\Models\LinenCycle;

/**
 * Gerbang state machine siklus londri linen. Alur yang diizinkan:
 *   dikirim_londri -> dicuci -> kembali_bersih
 *                            -> rusak_hilang
 * kembali_bersih dan rusak_hilang bersifat terminal (tidak bisa lanjut lagi).
 * Semua transisi status HARUS lewat service ini, controller tidak boleh
 * menulis status langsung ke Model::update() — supaya linen yang sudah
 * "rusak_hilang" tidak bisa disulap balik jadi "kembali_bersih" dsb.
 */
class LinenCycleService
{
    /**
     * Matriks transisi valid: status saat ini => daftar status tujuan yang boleh.
     */
    private const TRANSITIONS = [
        LinenCycle::STATUS_DIKIRIM_LONDRI => [LinenCycle::STATUS_DICUCI],
        LinenCycle::STATUS_DICUCI => [LinenCycle::STATUS_KEMBALI_BERSIH, LinenCycle::STATUS_RUSAK_HILANG],
        LinenCycle::STATUS_KEMBALI_BERSIH => [],
        LinenCycle::STATUS_RUSAK_HILANG => [],
    ];

    private const TERMINAL_STATUSES = [
        LinenCycle::STATUS_KEMBALI_BERSIH,
        LinenCycle::STATUS_RUSAK_HILANG,
    ];

    /**
     * Buat siklus londri baru. Siklus selalu mulai dari dikirim_londri —
     * membuat siklus langsung berstatus dicuci/kembali_bersih/rusak_hilang
     * ditolak supaya riwayat tahapan tidak bolong.
     */
    public function create(array $data): LinenCycle
    {
        $status = $data['status'] ?? LinenCycle::STATUS_DIKIRIM_LONDRI;

        abort_if(
            $status !== LinenCycle::STATUS_DIKIRIM_LONDRI,
            422,
            'Siklus baru wajib mulai dari status dikirim_londri.',
        );

        return DB::transaction(function () use ($data) {
            return LinenCycle::create([
                'linen_item_id' => $data['linen_item_id'],
                'status' => LinenCycle::STATUS_DIKIRIM_LONDRI,
                'sent_at' => $data['sent_at'] ?? now(),
                'received_at' => null,
                'quantity' => $data['quantity'] ?? 1,
            ]);
        });
    }

    /**
     * Update siklus. Bila field status berubah, wajib lewat matriks
     * TRANSITIONS. received_at otomatis diisi now() saat masuk status
     * terminal (kembali_bersih/rusak_hilang) kalau belum dikirim eksplisit.
     */
    public function update(int $linenCycleId, array $data): LinenCycle
    {
        return DB::transaction(function () use ($linenCycleId, $data) {
            $cycle = LinenCycle::query()->lockForUpdate()->findOrFail($linenCycleId);

            if (array_key_exists('status', $data) && $data['status'] !== $cycle->status) {
                $allowed = self::TRANSITIONS[$cycle->status] ?? [];

                abort_if(
                    ! in_array($data['status'], $allowed, true),
                    422,
                    "Transisi status dari {$cycle->status} ke {$data['status']} tidak diizinkan.",
                );

                $cycle->status = $data['status'];

                if (in_array($data['status'], self::TERMINAL_STATUSES, true) && empty($data['received_at'])) {
                    $data['received_at'] = now();
                }
            }

            if (array_key_exists('sent_at', $data)) {
                $cycle->sent_at = $data['sent_at'];
            }

            if (array_key_exists('received_at', $data)) {
                $cycle->received_at = $data['received_at'];
            }

            if (array_key_exists('quantity', $data)) {
                $cycle->quantity = $data['quantity'];
            }

            $cycle->save();

            return $cycle;
        });
    }

    /**
     * Hapus siklus. Ditolak bila siklus masih berjalan (belum terminal) —
     * mencegah kehilangan jejak linen yang masih ada di londri.
     */
    public function delete(int $linenCycleId): void
    {
        DB::transaction(function () use ($linenCycleId) {
            $cycle = LinenCycle::query()->lockForUpdate()->findOrFail($linenCycleId);

            abort_if(
                ! in_array($cycle->status, self::TERMINAL_STATUSES, true),
                422,
                "Siklus dengan status {$cycle->status} masih berjalan, tidak bisa dihapus.",
            );

            $cycle->delete();
        });
    }
}
