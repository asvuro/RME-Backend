<?php

namespace Modules\GeneralSitbReferrerType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbReferrerTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 92).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Inisiatif Pasien / Keluarga',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Anggota Masyarakat / Kader',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Faskes',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Dokter Praktek Mandiri',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Poli Lain',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Lain - lain',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_referrer_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}