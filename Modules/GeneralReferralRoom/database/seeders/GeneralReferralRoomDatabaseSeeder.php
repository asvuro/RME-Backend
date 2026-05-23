<?php

namespace Modules\GeneralReferralRoom\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralReferralRoomDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 70).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => 'BEDAH',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'ANAK',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'ANAK ALERGI IMUNOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'ANAK ENDOKRINOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'ANAK GASTRO-HEPATOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'ANAK HEMATOLOGI ONKOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'ANAK INFEKSI & PEDIATRI TROPIS',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'ANAK KARDIOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'ANAK NEFROLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'ANAK NEUROLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => 'ANAK NUTRISI & PENYAKIT METABOLIK',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => 'PENCITRAAN ANAK ',
    'code' => NULL,
    'is_active' => true,
  ),
  12 => 
  array (
    'name' => 'RESPIROLOGI ANAK ',
    'code' => NULL,
    'is_active' => true,
  ),
  13 => 
  array (
    'name' => 'MANAJEMEN NYERI',
    'code' => NULL,
    'is_active' => true,
  ),
  14 => 
  array (
    'name' => 'ANASTESI',
    'code' => NULL,
    'is_active' => true,
  ),
  15 => 
  array (
    'name' => 'BEDAH ANAK',
    'code' => NULL,
    'is_active' => true,
  ),
  16 => 
  array (
    'name' => 'BEDAH ONKOLOGI ',
    'code' => NULL,
    'is_active' => true,
  ),
  17 => 
  array (
    'name' => 'BEDAH DIGESTIF ',
    'code' => NULL,
    'is_active' => true,
  ),
  18 => 
  array (
    'name' => 'HAND (BEDAH TANGAN)',
    'code' => NULL,
    'is_active' => true,
  ),
  19 => 
  array (
    'name' => 'GIGI BEDAH MULUT',
    'code' => NULL,
    'is_active' => true,
  ),
  20 => 
  array (
    'name' => 'BEDAH PLASTIK',
    'code' => NULL,
    'is_active' => true,
  ),
  21 => 
  array (
    'name' => 'BEDAH SARAF',
    'code' => NULL,
    'is_active' => true,
  ),
  22 => 
  array (
    'name' => 'BTKV (BEDAH THORAX KARDIOVASKU',
    'code' => NULL,
    'is_active' => true,
  ),
  23 => 
  array (
    'name' => 'ENDOKRIN-METABOLIK-DIABETES',
    'code' => NULL,
    'is_active' => true,
  ),
  24 => 
  array (
    'name' => 'ENDOKRINOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  25 => 
  array (
    'name' => 'GIGI ENDODONSI',
    'code' => NULL,
    'is_active' => true,
  ),
  26 => 
  array (
    'name' => 'NEUROFISIOLOGI KLINIS',
    'code' => NULL,
    'is_active' => true,
  ),
  27 => 
  array (
    'name' => 'GASTROENTEROLOGI-HEPATOLOGI ',
    'code' => NULL,
    'is_active' => true,
  ),
  28 => 
  array (
    'name' => 'GERIATRI ',
    'code' => NULL,
    'is_active' => true,
  ),
  29 => 
  array (
    'name' => 'NEUROBEHAVIOUR, MD, NEUROGERIATRI, DAN NEURORESTORASI',
    'code' => NULL,
    'is_active' => true,
  ),
  30 => 
  array (
    'name' => 'MICRO SURGERY',
    'code' => NULL,
    'is_active' => true,
  ),
  31 => 
  array (
    'name' => 'GIGI',
    'code' => NULL,
    'is_active' => true,
  ),
  32 => 
  array (
    'name' => 'GIGI ORTHODONTI',
    'code' => NULL,
    'is_active' => true,
  ),
  33 => 
  array (
    'name' => 'GIGI PERIODONTI',
    'code' => NULL,
    'is_active' => true,
  ),
  34 => 
  array (
    'name' => 'GIGI RADIOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  35 => 
  array (
    'name' => 'GIGI PEDODONTIS',
    'code' => NULL,
    'is_active' => true,
  ),
  36 => 
  array (
    'name' => 'GIGI PENYAKIT MULUT',
    'code' => NULL,
    'is_active' => true,
  ),
  37 => 
  array (
    'name' => 'GIGI PROSTHODONTI',
    'code' => NULL,
    'is_active' => true,
  ),
  38 => 
  array (
    'name' => 'GINJAL-HIPERTENSI ',
    'code' => NULL,
    'is_active' => true,
  ),
  39 => 
  array (
    'name' => 'ONKOLOGI GINEKOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  40 => 
  array (
    'name' => 'UROGINEKOLOGI REKONTRUSKI',
    'code' => NULL,
    'is_active' => true,
  ),
  41 => 
  array (
    'name' => 'OBSTETRI GINEKOLOGI SOSIAL',
    'code' => NULL,
    'is_active' => true,
  ),
  42 => 
  array (
    'name' => 'GIZI KLINIK',
    'code' => NULL,
    'is_active' => true,
  ),
  43 => 
  array (
    'name' => 'HEMODIALISA',
    'code' => NULL,
    'is_active' => true,
  ),
  44 => 
  array (
    'name' => 'INTENSIVE CARE/ICU ',
    'code' => NULL,
    'is_active' => true,
  ),
  45 => 
  array (
    'name' => 'ANESTESI REGIONAL DAN INTERVENSI',
    'code' => NULL,
    'is_active' => true,
  ),
  46 => 
  array (
    'name' => 'SEREBROVASKULAR, NEUROSONOLOGI, DAN NEUROLOGI INTERVENSI',
    'code' => NULL,
    'is_active' => true,
  ),
  47 => 
  array (
    'name' => 'NEURO-INTENSIF',
    'code' => NULL,
    'is_active' => true,
  ),
  48 => 
  array (
    'name' => 'PULMONOLOGI INTERVENSI DAN GAWAT DARURAT NAPAS',
    'code' => NULL,
    'is_active' => true,
  ),
  49 => 
  array (
    'name' => 'PENYAKIT DALAM',
    'code' => NULL,
    'is_active' => true,
  ),
  50 => 
  array (
    'name' => 'PENYAKIT TROPIK-INFEKSI ',
    'code' => NULL,
    'is_active' => true,
  ),
  51 => 
  array (
    'name' => 'REHABILITASI MEDIK',
    'code' => NULL,
    'is_active' => true,
  ),
  52 => 
  array (
    'name' => 'JANTUNG DAN PEMBULUH DARAH',
    'code' => NULL,
    'is_active' => true,
  ),
  53 => 
  array (
    'name' => 'JIWA',
    'code' => NULL,
    'is_active' => true,
  ),
  54 => 
  array (
    'name' => 'KARDIOVASKULAR ',
    'code' => NULL,
    'is_active' => true,
  ),
  55 => 
  array (
    'name' => 'ANESTESI KARDIOVASKULER',
    'code' => NULL,
    'is_active' => true,
  ),
  56 => 
  array (
    'name' => 'NEUROMUSKULAR, SARAF PERIFER',
    'code' => NULL,
    'is_active' => true,
  ),
  57 => 
  array (
    'name' => 'KULIT KELAMIN',
    'code' => NULL,
    'is_active' => true,
  ),
  58 => 
  array (
    'name' => 'HEMATOLOGI - ONKOLOGI MEDIK ',
    'code' => NULL,
    'is_active' => true,
  ),
  59 => 
  array (
    'name' => 'PSIKOSOMATIK ',
    'code' => NULL,
    'is_active' => true,
  ),
  60 => 
  array (
    'name' => 'FETOMATERNAL',
    'code' => NULL,
    'is_active' => true,
  ),
  61 => 
  array (
    'name' => 'REUMATOLOGI ',
    'code' => NULL,
    'is_active' => true,
  ),
  62 => 
  array (
    'name' => 'MATA',
    'code' => NULL,
    'is_active' => true,
  ),
  63 => 
  array (
    'name' => 'OBGYN',
    'code' => NULL,
    'is_active' => true,
  ),
  64 => 
  array (
    'name' => 'ANESTESI OBSTETRI ',
    'code' => NULL,
    'is_active' => true,
  ),
  65 => 
  array (
    'name' => 'FAAL PARU KLINIK',
    'code' => NULL,
    'is_active' => true,
  ),
  66 => 
  array (
    'name' => 'PARU KERJA DAN LINGKUNGAN',
    'code' => NULL,
    'is_active' => true,
  ),
  67 => 
  array (
    'name' => 'PARU',
    'code' => NULL,
    'is_active' => true,
  ),
  68 => 
  array (
    'name' => 'PEDIATRI GAWAT DARURAT',
    'code' => NULL,
    'is_active' => true,
  ),
  69 => 
  array (
    'name' => 'INSTALASI GAWAT DARURAT',
    'code' => NULL,
    'is_active' => true,
  ),
  70 => 
  array (
    'name' => 'KESEHATAN REMAJA',
    'code' => NULL,
    'is_active' => true,
  ),
  71 => 
  array (
    'name' => 'SARAF',
    'code' => NULL,
    'is_active' => true,
  ),
  72 => 
  array (
    'name' => 'THT KOMUNITAS',
    'code' => NULL,
    'is_active' => true,
  ),
  73 => 
  array (
    'name' => 'THT-KL',
    'code' => NULL,
    'is_active' => true,
  ),
  74 => 
  array (
    'name' => 'NEUROANESTESI',
    'code' => NULL,
    'is_active' => true,
  ),
  75 => 
  array (
    'name' => 'NEUROTOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  76 => 
  array (
    'name' => 'NEUROTRAUMA',
    'code' => NULL,
    'is_active' => true,
  ),
  77 => 
  array (
    'name' => 'NEUROINFEKSI',
    'code' => NULL,
    'is_active' => true,
  ),
  78 => 
  array (
    'name' => 'NEUROINFEKSI DAN IMUNOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  79 => 
  array (
    'name' => 'NEURO-OFTALMOLOGI DAN NEURO-OTOLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
  80 => 
  array (
    'name' => 'NEUROPEDIATRI DAN NEUROKOMUNITASI',
    'code' => NULL,
    'is_active' => true,
  ),
  81 => 
  array (
    'name' => 'UROLOGI',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('referral_rooms')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}