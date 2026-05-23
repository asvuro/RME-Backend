<?php

namespace Modules\GeneralVisitType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralVisitTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 15).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Bukan Ruangan Kunjungan / Pelayanan',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Rawat Jalan',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Gawat Darurat (Observasi)',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Rawat Inap',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Laboratorium',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Radiologi',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Kamar Operasi',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Hemodialisa',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'Endoscopy',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'Litotripsi',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => 'Hiperbarik',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => 'Farmasi',
    'code' => NULL,
    'is_active' => true,
  ),
  12 => 
  array (
    'name' => 'Kamar Bersalin',
    'code' => NULL,
    'is_active' => true,
  ),
  13 => 
  array (
    'name' => 'Patologi Anatomi',
    'code' => NULL,
    'is_active' => true,
  ),
  14 => 
  array (
    'name' => 'Radioterapi',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('visit_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}