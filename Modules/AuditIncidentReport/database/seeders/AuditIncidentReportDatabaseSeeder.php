<?php

namespace Modules\AuditIncidentReport\Database\Seeders;

use Illuminate\Database\Seeder;

class AuditIncidentReportDatabaseSeeder extends Seeder
{
    /**
     * Laporan IKP adalah data operasional yang diinput petugas lewat API —
     * tidak ada master data untuk di-seed (beda dgn modul katalog seperti
     * GeneralAbsenceType). Seeder tetap disediakan agar struktur modul seragam.
     */
    public function run(): void {}
}
