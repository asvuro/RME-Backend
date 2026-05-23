<?php

namespace Modules\GeneralReservationStatus\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralReservationStatusDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 21).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Batal Reservasi',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Reservasi',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Selesai',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('reservation_statuses')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}