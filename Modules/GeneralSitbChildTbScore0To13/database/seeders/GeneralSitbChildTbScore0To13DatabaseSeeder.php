<?php

namespace Modules\GeneralSitbChildTbScore0To13\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbChildTbScore0To13DatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 97).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => '0',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => '1',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => '2',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => '3',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => '4',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => '5',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => '6',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => '7',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => '8',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => '9',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => '10',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => '11',
    'code' => NULL,
    'is_active' => true,
  ),
  12 => 
  array (
    'name' => '12',
    'code' => NULL,
    'is_active' => true,
  ),
  13 => 
  array (
    'name' => '13',
    'code' => NULL,
    'is_active' => true,
  ),
  14 => 
  array (
    'name' => 'Tidak dilakukan',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_child_tb_score0_to13s')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}