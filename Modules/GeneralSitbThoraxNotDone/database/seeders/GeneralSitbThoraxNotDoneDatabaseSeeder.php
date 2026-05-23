<?php

namespace Modules\GeneralSitbThoraxNotDone\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbThoraxNotDoneDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 118).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Tidak dilakukan',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Setelah terapi antibiotika non OAT: tidak ada perbaikan Klinis, ada faktor resiko TB, dan atas pertimbangan dokter',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Setelah terapi antibiotika non OAT: ada Perbaikan Klinis',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_thorax_not_dones')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}