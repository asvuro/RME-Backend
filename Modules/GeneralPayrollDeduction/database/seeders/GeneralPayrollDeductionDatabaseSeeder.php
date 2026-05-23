<?php

namespace Modules\GeneralPayrollDeduction\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPayrollDeductionDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 90).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Pajak Penghasilan (PPh Pasal 21)',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Jaminan Hari Tua (JHT 3.7%)',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Jaminan Hari Tua (JHT 2%)',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Jaminan Pelayanan Kesehatan (JPK Pasal 6)',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Jaminan Kecelakaan Kerja (JKK)',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Jaminan Kematian (JKM)',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Pinjaman Bank',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('payroll_deductions')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}