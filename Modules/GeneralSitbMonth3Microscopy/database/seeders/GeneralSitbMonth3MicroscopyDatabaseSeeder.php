<?php

namespace Modules\GeneralSitbMonth3Microscopy\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbMonth3MicroscopyDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 107).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Positif',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Negatif',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Tidak diketahui',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_month3_microscopies')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}