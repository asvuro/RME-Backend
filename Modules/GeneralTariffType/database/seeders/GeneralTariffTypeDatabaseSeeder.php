<?php

namespace Modules\GeneralTariffType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralTariffTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 30).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Administrasi',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Akomodasi / Ruang Perawatan (Rawat Inap)',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Tindakan',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Farmasi',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Paket',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'O2',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('tariff_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}