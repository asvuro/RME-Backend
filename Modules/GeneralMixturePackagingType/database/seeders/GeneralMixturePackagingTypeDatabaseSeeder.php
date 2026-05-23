<?php

namespace Modules\GeneralMixturePackagingType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralMixturePackagingTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 79).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Puyer',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Kapsul',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Tube',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('mixture_packaging_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}