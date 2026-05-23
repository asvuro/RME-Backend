<?php

namespace Modules\GeneralSitbTreatmentOutcome\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbTreatmentOutcomeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 110).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Sembuh',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Pengobatan Lengkap',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Lost to follow up',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Meninggal',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Gagal',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Pindah',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_treatment_outcomes')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}