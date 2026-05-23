<?php

namespace App\Modules\Contracts;

use Modules\Auth\Models\User;

/**
 * Kontrak lintas modul untuk mutasi stok per unit/ward (port
 * inventory.transaksi_stok_ruangan simgos2). Modul farmasi mengurangi stok
 * lewat kontrak ini tanpa meng-import model modul inventori langsung.
 */
interface StockGate
{
    /**
     * Catat mutasi ke ledger dan perbarui saldo dalam satu transaksi.
     *
     * @param  string  $type  in|out|adjustment (port JENIS transaksi stok ruangan)
     * @param  int  $quantity  selalu positif; arah ditentukan type (out = pengurangan)
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException bila hasil
     *                                                                saldo negatif dan config pharmacy.allow_order_out_of_stock FALSE.
     */
    public function adjust(int $wardId, int $itemId, string $type, int $quantity, User $user, ?string $notes = null): void;

    /** Saldo berjalan saat ini (port getStokAkhir). */
    public function currentStock(int $wardId, int $itemId): int;
}
