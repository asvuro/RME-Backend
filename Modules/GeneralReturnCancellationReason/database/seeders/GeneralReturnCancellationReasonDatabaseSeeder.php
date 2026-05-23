<?php

namespace Modules\GeneralReturnCancellationReason\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralReturnCancellationReasonDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 59).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Kesalahan / Kelalaian / Kelupaan',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Kesalahan Pasien / Pelanggan',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('return_cancellation_reasons')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}