<?php

namespace Modules\GeneralSitbDrugSource\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbDrugSourceDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 101).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Program TB',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Bayar Sendiri',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Asuransi',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Lain - lain',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_drug_sources')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}