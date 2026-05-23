<?php

namespace Modules\GeneralContactType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralContactTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 8).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Telepon Rumah',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Telepon Kantor',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Telepon Seluler',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Faks Rumah',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Faks Kantor',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Email',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Situs Web',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'IM / Alamat Media Sosial',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('contact_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}