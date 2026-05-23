<?php

namespace Modules\GeneralFacilityOwnershipType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralFacilityOwnershipTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 28).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Kementrian Kesehatan',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Kementrian Lainnya',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Pemerintah Propinsi',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Pemerintah Kota',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Pemerintah Kabupaten',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'BUMN',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Organisasi',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Organisasi Islam',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'Organisasi Katolik',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'Organisasi Protestan',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => 'Organisasi Sosial',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => 'Perorangan',
    'code' => NULL,
    'is_active' => true,
  ),
  12 => 
  array (
    'name' => 'Perusahaan',
    'code' => NULL,
    'is_active' => true,
  ),
  13 => 
  array (
    'name' => 'Swasta',
    'code' => NULL,
    'is_active' => true,
  ),
  14 => 
  array (
    'name' => 'POLRI',
    'code' => NULL,
    'is_active' => true,
  ),
  15 => 
  array (
    'name' => 'TNI AD',
    'code' => NULL,
    'is_active' => true,
  ),
  16 => 
  array (
    'name' => 'TNI AU',
    'code' => NULL,
    'is_active' => true,
  ),
  17 => 
  array (
    'name' => 'TNI AL',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('facility_ownership_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}