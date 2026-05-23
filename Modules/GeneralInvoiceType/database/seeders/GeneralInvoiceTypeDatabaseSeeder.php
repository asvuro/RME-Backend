<?php

namespace Modules\GeneralInvoiceType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralInvoiceTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 49).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Pasien',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Piutang Pasien',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Piutang Perusahaan',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Penjualan',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('invoice_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}