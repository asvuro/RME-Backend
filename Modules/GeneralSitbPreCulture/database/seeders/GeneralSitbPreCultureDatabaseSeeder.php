<?php

namespace Modules\GeneralSitbPreCulture\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbPreCultureDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 105).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Negatif',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => '1-19 BTA',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => '1+',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => '2+',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => '3+',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => '4+',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'NTM',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Kontaminasi',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'Tidak dilakukan',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_pre_cultures')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}