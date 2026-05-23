<?php

namespace Modules\GeneralMonthName\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralMonthNameDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 83).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Januari',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Februari',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Maret',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'April',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Mei',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Juni',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Juli',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Agustus',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'September',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'Oktober',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => 'Nopember',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => 'Desember',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('month_names')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}