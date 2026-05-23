<?php

namespace Modules\GeneralPackageItemType\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralPackageItemTypeDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 37).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Layanan Tindakan',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Farmasi',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Administrasi',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'O2',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('package_item_types')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}