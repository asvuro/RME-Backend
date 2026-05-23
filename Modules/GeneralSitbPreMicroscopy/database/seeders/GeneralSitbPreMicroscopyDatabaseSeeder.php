<?php

namespace Modules\GeneralSitbPreMicroscopy\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbPreMicroscopyDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 103).
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
            DB::table('sitb_pre_microscopies')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}