<?php

namespace Modules\GeneralUserGroup\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralUserGroupDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 43).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Admin SIMRS',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Petugas Pendaftaran Rawat Jalan',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Petugas Pendaftaran IGD',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Petugas Pendaftaran Rawat Inap',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Admin Pendaftaran',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Petugas Layanan Rawat Jalan',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Petugas Layanan IGD',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Petugas Layanan Rawat Inap',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'Admin Petugas Layanan',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'Petugas Apotik Farmasi',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => 'Petugas Gudang Farmasi',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => 'Admin Farmasi',
    'code' => NULL,
    'is_active' => true,
  ),
  12 => 
  array (
    'name' => 'Petugas Kasir',
    'code' => NULL,
    'is_active' => true,
  ),
  13 => 
  array (
    'name' => 'Admin Kasir',
    'code' => NULL,
    'is_active' => true,
  ),
  14 => 
  array (
    'name' => 'Petugas Coder Rekam Medis',
    'code' => NULL,
    'is_active' => true,
  ),
  15 => 
  array (
    'name' => 'Petugas Penyimpanan Rekam Medis',
    'code' => NULL,
    'is_active' => true,
  ),
  16 => 
  array (
    'name' => 'Admin Rekam Medis',
    'code' => NULL,
    'is_active' => true,
  ),
  17 => 
  array (
    'name' => 'Petugas Keuangan',
    'code' => NULL,
    'is_active' => true,
  ),
  18 => 
  array (
    'name' => 'Admin Keuangan',
    'code' => NULL,
    'is_active' => true,
  ),
  19 => 
  array (
    'name' => 'Petugas Layanan Laboratorium',
    'code' => NULL,
    'is_active' => true,
  ),
  20 => 
  array (
    'name' => 'Petugas Layanan Radiologi',
    'code' => NULL,
    'is_active' => true,
  ),
  21 => 
  array (
    'name' => 'Informasi',
    'code' => NULL,
    'is_active' => true,
  ),
  22 => 
  array (
    'name' => 'Petugas Layanan Kamar Bersalin',
    'code' => NULL,
    'is_active' => true,
  ),
  23 => 
  array (
    'name' => 'Inventory',
    'code' => NULL,
    'is_active' => true,
  ),
  24 => 
  array (
    'name' => 'Inventory (Non Farmasi)',
    'code' => NULL,
    'is_active' => true,
  ),
  25 => 
  array (
    'name' => 'Petugas Inventory Non Farmasi',
    'code' => NULL,
    'is_active' => true,
  ),
  26 => 
  array (
    'name' => 'Pencarian',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('user_groups')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}