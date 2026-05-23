<?php

namespace Modules\GeneralReferralType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralReferralTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 86).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Rujuk Balik',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Partial',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Penuh',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('referral_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}