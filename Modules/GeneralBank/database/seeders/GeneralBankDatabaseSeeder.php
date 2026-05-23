<?php

namespace Modules\GeneralBank\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralBankDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 16).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Bank Tabungan Negara (BTN)',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Bank Rakyat Indonesia (BRI)',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Bank Negara Indonesia (BNI)',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Bank Mandiri',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Bank Central Asia (BCA)',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('banks')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}