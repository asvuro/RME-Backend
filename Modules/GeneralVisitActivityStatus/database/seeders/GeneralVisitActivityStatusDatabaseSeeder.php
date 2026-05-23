<?php

namespace Modules\GeneralVisitActivityStatus\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralVisitActivityStatusDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 31).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Batal Kunjungan',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Pasien Berada di ruangan ini / Sedang dilayani',
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
            DB::table('visit_activity_statuses')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}