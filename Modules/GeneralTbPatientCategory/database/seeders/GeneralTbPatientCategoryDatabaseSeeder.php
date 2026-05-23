<?php

namespace Modules\GeneralTbPatientCategory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralTbPatientCategoryDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 61).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'A. SISA PENDERITA KEMARIN',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'B. PENDERITA MASUK',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'C. PENDERITA PINDAHAN',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'D. PENDERITA KELUAR',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'E. PENDERITA MENINGGAL',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'F. PENDERITA DIPINDAHKAN',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'RINCIAN HARI PERAWATAN',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('tb_patient_categories')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}