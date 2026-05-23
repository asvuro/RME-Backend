<?php

namespace Modules\GeneralMedicalPersonnelType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralMedicalPersonnelTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 32).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Dokter',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Anastesi',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Paramedis',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Apoteker',
    'code' => NULL,
    'is_active' => false,
  ),
  4 => 
  array (
    'name' => 'Bidan',
    'code' => NULL,
    'is_active' => false,
  ),
  5 => 
  array (
    'name' => 'Analis',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Penata Anastesi',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('medical_personnel_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}