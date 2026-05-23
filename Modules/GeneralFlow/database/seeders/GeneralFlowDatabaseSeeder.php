<?php

namespace Modules\GeneralFlow\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralFlowDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 51).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => '0.5',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => '1.0',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => '2.0',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => '3.0',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => '4.0',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => '5.0',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => '6.0',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => '7.0',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => '8.0',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => '9.0',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => '10.0',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => '11.0',
    'code' => NULL,
    'is_active' => true,
  ),
  12 => 
  array (
    'name' => '12.0',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('flows')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}