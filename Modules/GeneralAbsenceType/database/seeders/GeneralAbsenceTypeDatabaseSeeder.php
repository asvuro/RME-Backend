<?php

namespace Modules\GeneralAbsenceType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralAbsenceTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 77).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Izin',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Cuti',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Sakit',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Dinas Luar',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('absence_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}