<?php

namespace Modules\GeneralPrintType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPrintTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 27).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Baru',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Hilang / Rusak',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Tidak Tercetak',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Rusak pada saat Cetak',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('print_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}