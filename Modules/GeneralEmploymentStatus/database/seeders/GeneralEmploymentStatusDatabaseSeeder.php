<?php

namespace Modules\GeneralEmploymentStatus\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralEmploymentStatusDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 88).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'CPNS',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'PNS',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Kontrak',
    'code' => NULL,
    'is_active' => false,
  ),
  3 => 
  array (
    'name' => 'BLU',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'DIKNAS',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'VISITING',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('employment_statuses')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}