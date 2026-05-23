<?php

namespace Modules\GeneralOperationGroup\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralOperationGroupDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 44).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Kecil',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Sedang',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Besar',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Khusus',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('operation_groups')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}