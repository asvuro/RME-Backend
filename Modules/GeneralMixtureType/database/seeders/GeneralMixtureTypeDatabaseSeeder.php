<?php

namespace Modules\GeneralMixtureType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralMixtureTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 150).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Kapsul',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Puyer',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Informasi Pengunjung',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Footer Main Aplikasi',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('mixture_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}