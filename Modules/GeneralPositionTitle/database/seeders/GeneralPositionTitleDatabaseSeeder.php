<?php

namespace Modules\GeneralPositionTitle\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPositionTitleDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 18 & 76).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Direktur',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Wakil Direktur Keuangan',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Direktur Sumber Daya Manusia dan Pendidikan',
    'code' => NULL,
    'is_active' => false,
  ),
  3 => 
  array (
    'name' => 'Wakil Direktur Operasional',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Wakil Direktur Medik',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Kepala Pelayanan Keperawatan',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Kepala Administrasi dan Rekam Medik',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Kepala Fasilitas Penunjang Medik dan Non Medik',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'Sekretaris',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('position_titles')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}