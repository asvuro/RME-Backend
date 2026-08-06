<?php

namespace Modules\LayananLabAnalyzerOrder\Services;

use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananLabAnalyzerOrder\Models\LabAnalyzerOrder;

/**
 * State machine order analyzer LIS (versi tracking data, tanpa bridging
 * HL7/ASTM): ordered -> sent_to_analyzer -> result_received -> verified.
 *
 * Semua mutasi lewat sini; controller tidak pernah menulis Model langsung.
 * Setiap transisi dikunci lockForUpdate di dalam transaksi supaya dua request
 * paralel tidak bisa melompati gerbang status yang sama.
 */
class LabAnalyzerOrderService
{
    /**
     * Order baru selalu mulai dari 'ordered'. Status tidak pernah diterima dari
     * input klien (kolom pun tidak fillable di model).
     */
    public function create(array $data): LabAnalyzerOrder
    {
        $order = LabAnalyzerOrder::create([
            ...$data,
            'ordered_at' => $data['ordered_at'] ?? now(),
        ]);

        // create() tidak mengembalikan 'status' (default DB, bukan payload) --
        // refresh supaya atribut di memori sinkron dengan baris yang tersimpan.
        return $order->refresh();
    }

    /**
     * Detail order (test_code/vendor_id) hanya boleh diedit sebelum order
     * dikirim ke analyzer. Setelah terkirim, hasil mentah sudah jadi jejak
     * audit alat - mengubah urusan klinisnya berbahaya, jadi ditolak 422.
     */
    public function update(int $orderId, array $data): LabAnalyzerOrder
    {
        return DB::transaction(function () use ($orderId, $data) {
            $order = LabAnalyzerOrder::query()->lockForUpdate()->findOrFail($orderId);

            abort_if(
                $order->status !== LabAnalyzerOrder::STATUS_ORDERED,
                422,
                'Order sudah dikirim ke analyzer dan tidak bisa diedit lagi.',
            );

            $order->update($data);

            return $order;
        });
    }

    /**
     * Hapus hanya boleh untuk salah ketik sebelum pengiriman. Setelah order
     * masuk ke analyzer (atau sudah diverifikasi), jejaknya harus tinggal.
     */
    public function destroy(int $orderId): void
    {
        DB::transaction(function () use ($orderId) {
            $order = LabAnalyzerOrder::query()->lockForUpdate()->findOrFail($orderId);

            abort_if(
                $order->status !== LabAnalyzerOrder::STATUS_ORDERED,
                422,
                "Order #{$orderId} sudah diproses analyzer dan tidak bisa dihapus.",
            );

            $order->delete();
        });
    }

    /**
     * Gerbang 1: hanya order berstatus 'ordered' yang boleh dikirim ke analyzer.
     */
    public function sendToAnalyzer(int $orderId): LabAnalyzerOrder
    {
        return DB::transaction(function () use ($orderId) {
            $order = LabAnalyzerOrder::query()->lockForUpdate()->findOrFail($orderId);

            abort_if(
                $order->status !== LabAnalyzerOrder::STATUS_ORDERED,
                422,
                "Order #{$orderId} hanya bisa dikirim ke analyzer dari status ordered.",
            );

            $order->forceFill(['status' => LabAnalyzerOrder::STATUS_SENT_TO_ANALYZER])->save();

            return $order;
        });
    }

    /**
     * Gerbang 2: hasil mentah hanya diterima dari status sent_to_analyzer.
     * raw_result_text disimpan apa adanya - modul ini tidak mem-parse protokol.
     */
    public function recordResult(int $orderId, string $rawResultText): LabAnalyzerOrder
    {
        return DB::transaction(function () use ($orderId, $rawResultText) {
            $order = LabAnalyzerOrder::query()->lockForUpdate()->findOrFail($orderId);

            abort_if(
                $order->status !== LabAnalyzerOrder::STATUS_SENT_TO_ANALYZER,
                422,
                "Order #{$orderId} belum dikirim ke analyzer, tidak ada hasil untuk diterima.",
            );

            $order->fill(['raw_result_text' => $rawResultText]);
            $order->forceFill(['status' => LabAnalyzerOrder::STATUS_RESULT_RECEIVED]);
            $order->save();

            return $order;
        });
    }

    /**
     * Gerbang verifikasi (spesifikasi): HANYA dari result_received. Verifikator
     * dicatat sebagai employee + timestamp; verified_by tidak pernah dari input
     * klien supaya tidak bisa menempelkan nama orang lain.
     */
    public function verify(int $orderId, User $verifier): LabAnalyzerOrder
    {
        return DB::transaction(function () use ($orderId, $verifier) {
            $order = LabAnalyzerOrder::query()->lockForUpdate()->findOrFail($orderId);

            abort_if(
                $order->status !== LabAnalyzerOrder::STATUS_RESULT_RECEIVED,
                422,
                "Order #{$orderId} hanya bisa diverifikasi dari status result_received.",
            );

            // Defensif: state machine menjamin raw_result_text sudah ada di
            // status ini, tapi verifikasi tanpa isi hasil tetap ditolak.
            abort_if(
                $order->raw_result_text === null,
                422,
                "Hasil mentah order #{$orderId} kosong, tidak bisa diverifikasi.",
            );

            // Kolom verified_by menunjuk tabel employees (bukan users), jadi id
            // user login HARUS dipetakan ke profil pegawainya dulu. Menulis id
            // users langsung akan melanggar FK employees.id (konvensi modul lain:
            // AuditQualityIndicator & InventoryBloodBag resolve via user_id).
            $employeeId = Employee::query()
                ->where('user_id', $verifier->id)
                ->value('id');

            abort_if(
                $employeeId === null,
                422,
                'User login belum terhubung ke profil pegawai, tidak bisa memverifikasi hasil.',
            );

            $order->fill([
                'verified_by' => $employeeId,
                'verified_at' => now(),
            ]);
            $order->forceFill(['status' => LabAnalyzerOrder::STATUS_VERIFIED]);
            $order->save();

            return $order;
        });
    }
}
