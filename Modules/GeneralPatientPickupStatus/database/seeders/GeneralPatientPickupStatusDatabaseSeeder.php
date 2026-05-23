<?php

namespace Modules\GeneralPatientPickupStatus\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPatientPickupStatusDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 24).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Batal terima pasien',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Pasien belum diterima',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Pasien sudah diterima',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('patient_pickup_statuses')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}