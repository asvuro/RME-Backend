<?php

namespace Modules\GeneralPatientStatus\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPatientStatusDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 13).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Dibatalkan / Tidak Aktif',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Hidup / Aktif',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Meninggal',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('patient_statuses')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}