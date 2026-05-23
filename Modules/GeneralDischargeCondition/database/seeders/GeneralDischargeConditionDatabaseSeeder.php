<?php

namespace Modules\GeneralDischargeCondition\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralDischargeConditionDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 46).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Sembuh',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Membaik',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Belum Sembuh',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Meninggal <= 48 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Meninggal > 48 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'DOA',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('discharge_conditions')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}