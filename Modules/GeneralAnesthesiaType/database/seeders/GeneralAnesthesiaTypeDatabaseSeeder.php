<?php

namespace Modules\GeneralAnesthesiaType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralAnesthesiaTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 52).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Umum',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Spinal',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Epidural',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'BSP',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'CSE',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Lokal',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('anesthesia_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}