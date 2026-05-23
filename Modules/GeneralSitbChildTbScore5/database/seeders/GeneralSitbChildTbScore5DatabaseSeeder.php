<?php

namespace Modules\GeneralSitbChildTbScore5\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSitbChildTbScore5DatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 98).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'Uji tuberkulin Positif dan atau ada kontak TB Paru',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Uji tuberkulin Negatif dan atau tidak ada kontak TB Paru',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sitb_child_tb_score5s')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}