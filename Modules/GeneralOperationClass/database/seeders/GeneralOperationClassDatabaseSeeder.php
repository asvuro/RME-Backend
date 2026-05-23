<?php

namespace Modules\GeneralOperationClass\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralOperationClassDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 53).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Khusus',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Mayor',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Medium',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Minor',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Emergensi',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Elektif',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('operation_classes')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}