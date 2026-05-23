<?php

namespace Modules\GeneralFamilyRelationship\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralFamilyRelationshipDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 7).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Kepala Keluarga',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Suami',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Isteri',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Anak',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Menantu',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Cucu',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Orang Tua',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Mertua',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'Family Lain',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'Pembantu',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => 'Lainnya',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('family_relationships')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}