<?php

namespace Modules\GeneralMedicationAdministrationType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralMedicationAdministrationTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 78).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Resep',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Non Resep',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('medication_administration_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}