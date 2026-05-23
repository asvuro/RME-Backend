<?php

namespace Modules\GeneralIdentityCardType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralIdentityCardTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 9).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Kartu Tanda Penduduk (KTP)',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Surat Izin Mengemudi (SIM)',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Kartu Pelajar',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Passport',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Kartu Izin Tinggal Sementara (KITAS)',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Kartu Izin Tinggal Tetap (KITAP)',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'KTP WNA',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('identity_card_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}