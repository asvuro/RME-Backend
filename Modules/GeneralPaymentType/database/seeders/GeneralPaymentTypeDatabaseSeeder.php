<?php

namespace Modules\GeneralPaymentType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPaymentTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 34).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Umum',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Jaminan / Asuransi',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Ikatan Kerja Sama',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('payment_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}