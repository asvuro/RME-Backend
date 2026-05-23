<?php

namespace Modules\GeneralAccidentGuarantorType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralAccidentGuarantorTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 80).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'PT. Jasa Raharja',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'BPJS Ketenagakerjaan',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'PT. TASPEN',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'PT. ASABRI',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('accident_guarantor_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}