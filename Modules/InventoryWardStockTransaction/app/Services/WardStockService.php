<?php

namespace Modules\InventoryWardStockTransaction\Services;

use App\Modules\Contracts\HospitalConfig;
use App\Modules\Contracts\StockGate;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\InventoryWardItemStock\Models\WardItemStock;
use Modules\InventoryWardStockTransaction\Models\WardStockTransaction;

/**
 * Port inventory.transaksi_stok_ruangan + getStokAkhir simgos2: stok adalah
 * LEDGER berjalan; saldo (ward_item_stocks) adalah proyeksi yang dijaga
 * konsisten oleh service ini — tidak boleh ditulis langsung dari luar.
 */
class WardStockService implements StockGate
{
    public const TYPES = ['in', 'out', 'adjustment'];

    public function __construct(protected HospitalConfig $config) {}

    /** Arah mutasi per type: out mengurangi, sisanya menambah. */
    protected function signedQuantity(string $type, int $quantity): int
    {
        return match ($type) {
            'out' => -abs($quantity),
            default => abs($quantity),
        };
    }

    public function adjust(int $wardId, int $itemId, string $type, int $quantity, User $user, ?string $notes = null): void
    {
        $this->record($wardId, $itemId, $type, $quantity, $user, $notes);
    }

    /**
     * Sama seperti adjust(), tapi mengembalikan entri ledger yang dibuat.
     * Dipakai oleh endpoint yang perlu menyajikan transaksi hasil pencatatan
     * (mis. WardStockTransactionResource), tanpa menduplikasi invariant
     * (lockForUpdate, gerbang allow_order_out_of_stock, flooring saldo di 0).
     */
    public function record(int $wardId, int $itemId, string $type, int $quantity, User $user, ?string $notes = null, ?\DateTimeInterface $performedAt = null): WardStockTransaction
    {
        if (! in_array($type, self::TYPES, true)) {
            abort(422, "Tipe transaksi stok '{$type}' tidak dikenal.");
        }

        if ($quantity <= 0 && $type !== 'adjustment') {
            abort(422, 'Jumlah mutasi stok harus positif.');
        }

        // Ala SP simgos2 yang membaca config langsung: gerbang negatif
        // dikendalikan pharmacy.allow_order_out_of_stock (PropertiConfig 48).
        $allowNegative = (bool) $this->config->get('pharmacy.allow_order_out_of_stock', false);

        return DB::transaction(function () use ($wardId, $itemId, $type, $quantity, $user, $notes, $performedAt, $allowNegative) {
            $delta = $this->signedQuantity($type, $quantity);

            $stock = WardItemStock::query()
                ->where('ward_id', $wardId)
                ->where('item_id', $itemId)
                ->lockForUpdate()
                ->first();

            $newBalance = ($stock?->quantity ?? 0) + $delta;

            if ($newBalance < 0 && ! $allowNegative) {
                abort(422, "Stok tidak cukup: sisa {$newBalance} untuk item #{$itemId} di ward #{$wardId}.");
            }

            // Skema ledger memakai unsignedInteger: jumlah SELALU positif,
            // arah mutasi disimpan pada kolom type (out = pengurangan).
            $transaction = WardStockTransaction::create([
                'ward_id' => $wardId,
                'item_id' => $itemId,
                'type' => $type,
                'quantity' => abs($quantity),
                'performed_by' => $user->id,
                'performed_at' => $performedAt ?? now(),
                'notes' => $notes,
            ]);

            if ($stock === null) {
                WardItemStock::create(['ward_id' => $wardId, 'item_id' => $itemId, 'quantity' => max(0, $newBalance)]);
            } else {
                $stock->update(['quantity' => max(0, $newBalance)]);
            }

            return $transaction;
        });
    }

    public function currentStock(int $wardId, int $itemId): int
    {
        return (int) WardItemStock::query()
            ->where('ward_id', $wardId)
            ->where('item_id', $itemId)
            ->value('quantity');
    }
}
