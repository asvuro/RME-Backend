<?php

namespace Modules\GeneralPlanningPeriod\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPlanningPeriodDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 68).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => '1 Bulan Terakhir',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => '2 Bulan Terakhir',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => '3 Bulan Terakhir',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => '4 Bulan Terakhir',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('planning_periods')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}