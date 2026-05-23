<?php

namespace Modules\GeneralHealthProviderType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralHealthProviderTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 11).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Rumah Sakit',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Puskesmas',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Klinik',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Dokter',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Apotek',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Instansi',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Perusahaan',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('health_provider_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}