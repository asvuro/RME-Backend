<?php

namespace Modules\GeneralPaymentTransactionType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPaymentTransactionTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 50).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Pembayaran Tagihan Pasien Melalui Kasir / Tunai',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Pembayaran Tagihan Pasien Melalui EDC',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Pembayaran Tagihan Pasien Melalui Transfer / Bank',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Pembayaran Piutang Pasien Melalui Kasir',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Pembayaran Piutang Pasien Melalui EDC',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Pembayaran Piutang Pasien Melalui Transfer / Bank',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Pembayaran Piutang Perusahaan Melalui Transfer / Bank',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Penjualan (Pembayaran Tunai)',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('payment_transaction_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}