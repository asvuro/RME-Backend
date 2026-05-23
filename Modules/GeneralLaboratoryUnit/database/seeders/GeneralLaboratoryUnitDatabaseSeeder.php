<?php

namespace Modules\GeneralLaboratoryUnit\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralLaboratoryUnitDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 35).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'U/L',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'mg/dl',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => '%',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => '1000 ng/ml',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => '300 ng/ml',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => '50 ng/ml',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'detik',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'gr/dl',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'IU / ml',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'lpk',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => 'mm',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => 'mmHg',
    'code' => NULL,
    'is_active' => true,
  ),
  12 => 
  array (
    'name' => 'mmol/l',
    'code' => NULL,
    'is_active' => true,
  ),
  13 => 
  array (
    'name' => 'Negatif',
    'code' => NULL,
    'is_active' => true,
  ),
  14 => 
  array (
    'name' => 'ng/dl',
    'code' => NULL,
    'is_active' => true,
  ),
  15 => 
  array (
    'name' => 'RBC/ ul',
    'code' => NULL,
    'is_active' => true,
  ),
  16 => 
  array (
    'name' => 'U/ml',
    'code' => NULL,
    'is_active' => true,
  ),
  17 => 
  array (
    'name' => 'ug/dl',
    'code' => NULL,
    'is_active' => true,
  ),
  18 => 
  array (
    'name' => 'ul',
    'code' => NULL,
    'is_active' => true,
  ),
  19 => 
  array (
    'name' => 'menit',
    'code' => NULL,
    'is_active' => true,
  ),
  20 => 
  array (
    'name' => '10^3/ul',
    'code' => NULL,
    'is_active' => true,
  ),
  21 => 
  array (
    'name' => 'mo/dl',
    'code' => NULL,
    'is_active' => true,
  ),
  22 => 
  array (
    'name' => 'WBC/ul',
    'code' => NULL,
    'is_active' => true,
  ),
  23 => 
  array (
    'name' => 'fL',
    'code' => NULL,
    'is_active' => true,
  ),
  24 => 
  array (
    'name' => '10^6/uL',
    'code' => NULL,
    'is_active' => true,
  ),
  25 => 
  array (
    'name' => 'pg',
    'code' => NULL,
    'is_active' => true,
  ),
  26 => 
  array (
    'name' => 'mIU/ml',
    'code' => NULL,
    'is_active' => true,
  ),
  27 => 
  array (
    'name' => '/ul',
    'code' => NULL,
    'is_active' => true,
  ),
  28 => 
  array (
    'name' => 'ng / ml',
    'code' => NULL,
    'is_active' => true,
  ),
  29 => 
  array (
    'name' => 'IU/L',
    'code' => NULL,
    'is_active' => true,
  ),
  30 => 
  array (
    'name' => 'ng/l',
    'code' => NULL,
    'is_active' => true,
  ),
  31 => 
  array (
    'name' => 'mg/l',
    'code' => NULL,
    'is_active' => true,
  ),
  32 => 
  array (
    'name' => 'cc',
    'code' => NULL,
    'is_active' => true,
  ),
  33 => 
  array (
    'name' => 'mm^3',
    'code' => NULL,
    'is_active' => true,
  ),
  34 => 
  array (
    'name' => 'ml',
    'code' => NULL,
    'is_active' => true,
  ),
  35 => 
  array (
    'name' => 'cm',
    'code' => NULL,
    'is_active' => true,
  ),
  36 => 
  array (
    'name' => 'ul/ml',
    'code' => NULL,
    'is_active' => true,
  ),
  37 => 
  array (
    'name' => 'ml/menit',
    'code' => NULL,
    'is_active' => true,
  ),
  38 => 
  array (
    'name' => 'lpb',
    'code' => NULL,
    'is_active' => true,
  ),
  39 => 
  array (
    'name' => 'mg%',
    'code' => NULL,
    'is_active' => true,
  ),
  40 => 
  array (
    'name' => 'Vol%',
    'code' => NULL,
    'is_active' => true,
  ),
  41 => 
  array (
    'name' => 'COI',
    'code' => NULL,
    'is_active' => true,
  ),
  42 => 
  array (
    'name' => 'gr/24 jam',
    'code' => NULL,
    'is_active' => true,
  ),
  43 => 
  array (
    'name' => 'Testing Satuan',
    'code' => NULL,
    'is_active' => true,
  ),
  44 => 
  array (
    'name' => 'Satuan tes 2',
    'code' => NULL,
    'is_active' => true,
  ),
  45 => 
  array (
    'name' => 'sel / ul',
    'code' => NULL,
    'is_active' => true,
  ),
  46 => 
  array (
    'name' => 'mEq/24 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('laboratory_units')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}