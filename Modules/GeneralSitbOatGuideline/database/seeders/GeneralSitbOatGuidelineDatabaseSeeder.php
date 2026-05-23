<?php

namespace Modules\GeneralSitbOatGuideline\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbOatGuidelineDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 100).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Kategori 1',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Kategori 2',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Kategori Anak',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Lain-lain',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_oat_guidelines')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}