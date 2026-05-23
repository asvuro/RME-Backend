<?php

namespace Modules\GeneralQuarter\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralQuarterDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 91).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Triwulan I (Januari - Maret)',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Triwulan II (April - Juni)',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Triwulan III (Juli - September)',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Triwulan IV (Oktober - Desember)',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('quarters')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}