<?php

namespace Modules\GeneralSitbTreatmentHistoryClassification\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbTreatmentHistoryClassificationDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 95).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Baru',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Kambuh',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Diobati Setelah Gagal',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Diobati Setelah Putus Berobat',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Lain - lain',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Riwayat Pengobatan Sebelumnya Tidak Diketahui',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Pindahan',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_treatment_history_classifications')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}