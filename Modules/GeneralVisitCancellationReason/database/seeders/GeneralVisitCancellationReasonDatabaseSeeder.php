<?php

namespace Modules\GeneralVisitCancellationReason\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralVisitCancellationReasonDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 57).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Kesalahan / Kelalaian / Kelupaan Penginputan',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Pasien batal keluar / Tanggal Keluar Baru',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('visit_cancellation_reasons')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}