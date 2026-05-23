<?php

namespace Modules\GeneralBridgeType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralBridgeTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 145).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'VCLAIM',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'E-KLAIM',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'SISRUTE',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'PUSDATIN',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'DUKCAPIL',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'SITT',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('bridge_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}