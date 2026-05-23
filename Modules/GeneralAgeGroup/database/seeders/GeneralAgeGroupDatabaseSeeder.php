<?php

namespace Modules\GeneralAgeGroup\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralAgeGroupDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 33).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => '0-<=6 hr',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => '>6-<=28 hr',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => '>28 hr-<=1 th',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => '>1-<=4 th',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => '>4-<=14 th',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => '>14-<=24 th',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => '>24-<=44 th',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => '>44-<=64 th',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => '>64 th',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('age_groups')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}