<?php

namespace Modules\GeneralPainScaleMethod\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPainScaleMethodDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 71).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'NRS',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'BPS',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'NIPS',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'FLACC',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'VAS',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('pain_scale_methods')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}