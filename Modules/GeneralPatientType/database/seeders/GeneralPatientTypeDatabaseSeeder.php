<?php

namespace Modules\GeneralPatientType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPatientTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 73).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Lama',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Baru',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('patient_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}