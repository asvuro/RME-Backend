<?php

namespace Modules\LayananLabAnalyzerOrder\Database\Seeders;

use Illuminate\Database\Seeder;

class LayananLabAnalyzerOrderDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vendor analyzer (Novanet/Vanslab/Winacom) sengaja tidak di-seed default:
        // daftar vendor itu per-installasi, diisi lewat endpoint CRUD.
    }
}
