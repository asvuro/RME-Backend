<?php

namespace Modules\GeneralSitbPreTcm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbPreTcmDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 104).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Rif sensitif',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Rif resisten',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Negatif',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Rif Indeterminated',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Invalid',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Error',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'No Result',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Tidak dilakukan',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_pre_tcms')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}