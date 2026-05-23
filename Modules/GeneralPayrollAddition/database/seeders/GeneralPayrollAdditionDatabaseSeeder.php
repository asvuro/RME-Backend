<?php

namespace Modules\GeneralPayrollAddition\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPayrollAdditionDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 89).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Gaji Pokok',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Tunjangan',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Jasa',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Bonus/THR',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Jaminan Hari Tua (JHT 3.7%)',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Jaminan Pelayanan Kesehatan (JPK Pasal 6)',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Jaminan Kecelakaan Kerja (JKK)',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Jaminan Kematian (JKM)',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'Kinerja',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'Lembur',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('payroll_additions')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}