<?php

namespace Modules\GeneralAccommodationCalculationRule\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralAccommodationCalculationRuleDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 127).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Keluar dikurangi Masuk',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => '- Masuk Pertama di kenakan Akomodasi 1 harirn- Lewat jam 00:00:00 maka akan dikenakan 1 hari',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('accommodation_calculation_rules')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}