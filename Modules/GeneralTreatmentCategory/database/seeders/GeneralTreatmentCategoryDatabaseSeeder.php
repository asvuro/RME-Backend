<?php

namespace Modules\GeneralTreatmentCategory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralTreatmentCategoryDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 74).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Prosedur Non Bedah',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Prosedur Bedah',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Konsultasi',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Tenaga Ahli',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Penunjang',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Radiologi',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Laboratorium',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Bank Darah',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'Non Kategori',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'Rehabilitasi',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => 'Keperawatan',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('treatment_categories')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}