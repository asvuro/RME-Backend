<?php

namespace Modules\GeneralDosageInstruction\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralDosageInstructionDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 41).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => '2 kali sehari ......... tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => '1 x 2 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => '1 x 3 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => '1 x 6 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => '1 x 8 jam',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => '1 x 12 jam',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => '1 x 24 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => '4 kali sehari 1 Vial .......(mg / g) Intravena tiap 6 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => '1 kali sehari 1 Vial........(mg / g) Intramuscular tiap 24 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => '2 kali sehari 1 Vial.......(mg / g) Intramuscular tiap 12 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => '3 kali sehari 1 Vial........(mg / g) Intramuscular tiap 8 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => '4 kali sehari 1 Vial ......(mg / g) Intramuscular tiap 6 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  12 => 
  array (
    'name' => 'Infus......tetes Intravena tiap menit',
    'code' => NULL,
    'is_active' => true,
  ),
  13 => 
  array (
    'name' => '1 kali sehari.....Sendok obat (........mg/.......mL) tiap 24 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  14 => 
  array (
    'name' => '2 kali sehari......Sendok obat (.......mg/...... mL) tiap 12 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  15 => 
  array (
    'name' => '3 kali sehari.....Sendok obat (......mg/...... mL) tiap 8 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  16 => 
  array (
    'name' => '4 kali sehari.....Sendok obat (.......mg/..... mL) tiap 6 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  17 => 
  array (
    'name' => '1 kali sehari.......Sendok makan (......mg/...... mL) tiap 24 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  18 => 
  array (
    'name' => '2 kali sehari......Sendok makan (......mg/...... mL) tiap 12 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  19 => 
  array (
    'name' => '3 kali sehari......Sendok makan (.......mg/...... mL) tiap 8 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  20 => 
  array (
    'name' => '4 kali sehari......Sendok makan (.......mg/...... mL) tiap 6 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  21 => 
  array (
    'name' => '1 kali Sehari ......unit Subkutan tiap 24 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  22 => 
  array (
    'name' => '2 kali Sehari ......unit Subkutan tiap 12 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  23 => 
  array (
    'name' => '3 kali Sehari ......unit Subkutan tiap 8 Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  24 => 
  array (
    'name' => 'pagi......unit, siang.......unit, malam.......unit',
    'code' => NULL,
    'is_active' => true,
  ),
  25 => 
  array (
    'name' => 'Tetes Mata......kali sehari.......tetes pada mata........',
    'code' => NULL,
    'is_active' => true,
  ),
  26 => 
  array (
    'name' => 'Tetes Telinga......kali sehari.......tetes pada Telinga........',
    'code' => NULL,
    'is_active' => true,
  ),
  27 => 
  array (
    'name' => 'Tetes Hidung......kali sehari.......tetes pada Hidung........',
    'code' => NULL,
    'is_active' => true,
  ),
  28 => 
  array (
    'name' => 'Ditempel 1 Lembar tiap 3 Hari',
    'code' => NULL,
    'is_active' => true,
  ),
  29 => 
  array (
    'name' => 'Larutkan Dalam 1 Gelas Air',
    'code' => NULL,
    'is_active' => true,
  ),
  30 => 
  array (
    'name' => 'Salep Kulit dioles tipis -tipis.......kali sehari pada kulit yang sakit',
    'code' => NULL,
    'is_active' => true,
  ),
  31 => 
  array (
    'name' => 'Obat Kumur Jangan Ditelan',
    'code' => NULL,
    'is_active' => true,
  ),
  32 => 
  array (
    'name' => '......kali sehari.......supp dimasukkan dalam lubang anus tiap......Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  33 => 
  array (
    'name' => '.......kali sehari pada vaginal tiap......jam',
    'code' => NULL,
    'is_active' => true,
  ),
  34 => 
  array (
    'name' => '.......per Syringe Pump tiap .....Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  35 => 
  array (
    'name' => '........per Nebules tiap ......Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  36 => 
  array (
    'name' => 'Tiap Jam Satu Sendok Makan (15 mL)',
    'code' => NULL,
    'is_active' => true,
  ),
  37 => 
  array (
    'name' => 'Tiap 2 Jam Satu Sendok Makan (15 mL)',
    'code' => NULL,
    'is_active' => true,
  ),
  38 => 
  array (
    'name' => '........kali sehari........hisapan',
    'code' => NULL,
    'is_active' => true,
  ),
  39 => 
  array (
    'name' => '........kali sehari.......tetes di mulut',
    'code' => NULL,
    'is_active' => true,
  ),
  40 => 
  array (
    'name' => '......kali sehari......Tablet.......mg tiap......Jam',
    'code' => NULL,
    'is_active' => true,
  ),
  41 => 
  array (
    'name' => 'Untuk Spooling',
    'code' => NULL,
    'is_active' => true,
  ),
  42 => 
  array (
    'name' => 'Pro Renata Jika Demam Pada Suhu 37,8ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â°C Maksimal 3 kali 500 mg',
    'code' => NULL,
    'is_active' => true,
  ),
  43 => 
  array (
    'name' => 'Pro Renata Jika Nyeri Maksimal 3 kali 500 mg',
    'code' => NULL,
    'is_active' => true,
  ),
  44 => 
  array (
    'name' => 'pagi.........Tablet, siang.......Tablet, malam.......Tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  45 => 
  array (
    'name' => '.........kali sehari.......bungkus',
    'code' => NULL,
    'is_active' => true,
  ),
  46 => 
  array (
    'name' => '.......kali sehari........Kapsul',
    'code' => NULL,
    'is_active' => true,
  ),
  47 => 
  array (
    'name' => '3 kali sehari ......... tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  48 => 
  array (
    'name' => '1 X 1',
    'code' => NULL,
    'is_active' => true,
  ),
  49 => 
  array (
    'name' => '-',
    'code' => NULL,
    'is_active' => true,
  ),
  50 => 
  array (
    'name' => '1 X 1 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  51 => 
  array (
    'name' => '1 X 1 SACHET',
    'code' => NULL,
    'is_active' => true,
  ),
  52 => 
  array (
    'name' => '2 X 1/3 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  53 => 
  array (
    'name' => '3 X 1 BUNGKUS',
    'code' => NULL,
    'is_active' => true,
  ),
  54 => 
  array (
    'name' => '2 X 1 KAPSUL',
    'code' => NULL,
    'is_active' => true,
  ),
  55 => 
  array (
    'name' => '3 X 11/2 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  56 => 
  array (
    'name' => '2 X 1 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  57 => 
  array (
    'name' => '1 X 12',
    'code' => NULL,
    'is_active' => true,
  ),
  58 => 
  array (
    'name' => '1 X 1 (SEBELUM MAKAN)',
    'code' => NULL,
    'is_active' => true,
  ),
  59 => 
  array (
    'name' => '1 X 1 TABLET',
    'code' => NULL,
    'is_active' => true,
  ),
  60 => 
  array (
    'name' => '3 X 1 TABLET',
    'code' => NULL,
    'is_active' => true,
  ),
  61 => 
  array (
    'name' => '1 X SEHARI 1 KAPSUL',
    'code' => NULL,
    'is_active' => true,
  ),
  62 => 
  array (
    'name' => '1 X SEHARI 1 TABLET',
    'code' => NULL,
    'is_active' => true,
  ),
  63 => 
  array (
    'name' => '3 X 1 ',
    'code' => NULL,
    'is_active' => true,
  ),
  64 => 
  array (
    'name' => '3 X 1 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  65 => 
  array (
    'name' => '2 X sehari 1 kapsul',
    'code' => NULL,
    'is_active' => true,
  ),
  66 => 
  array (
    'name' => '2 x sehari 1 sendok',
    'code' => NULL,
    'is_active' => true,
  ),
  67 => 
  array (
    'name' => '2 X sehari 1 sachet',
    'code' => NULL,
    'is_active' => true,
  ),
  68 => 
  array (
    'name' => '3 x sehari 1 kapsul',
    'code' => NULL,
    'is_active' => true,
  ),
  69 => 
  array (
    'name' => '3 X sehari 1 tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  70 => 
  array (
    'name' => '3 x sehari 1 sendok makan',
    'code' => NULL,
    'is_active' => true,
  ),
  71 => 
  array (
    'name' => '1 X sehari 1 sendok obat ',
    'code' => NULL,
    'is_active' => true,
  ),
  72 => 
  array (
    'name' => '3 x sehari 1 bungkus',
    'code' => NULL,
    'is_active' => true,
  ),
  73 => 
  array (
    'name' => '2 x sehari 1 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  74 => 
  array (
    'name' => '2 X sehari 1 tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  75 => 
  array (
    'name' => '3 x sehari 1 bungkus (sesudah makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  76 => 
  array (
    'name' => '2 X 1 TABLET',
    'code' => NULL,
    'is_active' => true,
  ),
  77 => 
  array (
    'name' => '2 X 1 BUNGKUS',
    'code' => NULL,
    'is_active' => true,
  ),
  78 => 
  array (
    'name' => '3 x sehari  3/4 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  79 => 
  array (
    'name' => '3 x sehari  1 bungkus',
    'code' => NULL,
    'is_active' => true,
  ),
  80 => 
  array (
    'name' => '1 X SEHARI 1 tube bila kejang',
    'code' => NULL,
    'is_active' => true,
  ),
  81 => 
  array (
    'name' => '5 x sehari 1 bungkus',
    'code' => NULL,
    'is_active' => true,
  ),
  82 => 
  array (
    'name' => '2 X sehari  1 sachet',
    'code' => NULL,
    'is_active' => true,
  ),
  83 => 
  array (
    'name' => '3 x sehari sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  84 => 
  array (
    'name' => '1 x sehari  1 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  85 => 
  array (
    'name' => '1 x sehari  1 sachet',
    'code' => NULL,
    'is_active' => true,
  ),
  86 => 
  array (
    'name' => '3 x sehari  1/2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  87 => 
  array (
    'name' => '1 x sehari  1 supp   di rectal',
    'code' => NULL,
    'is_active' => true,
  ),
  88 => 
  array (
    'name' => '3 x sehari  1 sachet ',
    'code' => NULL,
    'is_active' => true,
  ),
  89 => 
  array (
    'name' => '2 X sehari  4 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  90 => 
  array (
    'name' => '3 x sehari  1 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  91 => 
  array (
    'name' => 'SEBELUM MAKAN',
    'code' => NULL,
    'is_active' => true,
  ),
  92 => 
  array (
    'name' => 'SESUDAH MAKAN',
    'code' => NULL,
    'is_active' => true,
  ),
  93 => 
  array (
    'name' => '2 X 3/4 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  94 => 
  array (
    'name' => '3 X 1 1/2 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  95 => 
  array (
    'name' => '2 X 1 1/2',
    'code' => NULL,
    'is_active' => true,
  ),
  96 => 
  array (
    'name' => '1x1',
    'code' => NULL,
    'is_active' => true,
  ),
  97 => 
  array (
    'name' => '2 kali 1',
    'code' => NULL,
    'is_active' => true,
  ),
  98 => 
  array (
    'name' => '3 x sehari 3/4 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  99 => 
  array (
    'name' => '1 X1 tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  100 => 
  array (
    'name' => '1 X SEHARI 1 BUNGKUS',
    'code' => NULL,
    'is_active' => true,
  ),
  101 => 
  array (
    'name' => '1 X SEHARI 1/2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  102 => 
  array (
    'name' => '1 X SEHARI 1 capsul',
    'code' => NULL,
    'is_active' => true,
  ),
  103 => 
  array (
    'name' => '3 x sehari dioles tipis-tipis',
    'code' => NULL,
    'is_active' => true,
  ),
  104 => 
  array (
    'name' => '3 X 1 KAPSUL',
    'code' => NULL,
    'is_active' => true,
  ),
  105 => 
  array (
    'name' => '3 x sehari 1 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  106 => 
  array (
    'name' => '3 x sehari 1 1/2 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  107 => 
  array (
    'name' => '1 X 24',
    'code' => NULL,
    'is_active' => true,
  ),
  108 => 
  array (
    'name' => '3 x sehari   1 bungkus',
    'code' => NULL,
    'is_active' => true,
  ),
  109 => 
  array (
    'name' => '2 x sehari  1 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  110 => 
  array (
    'name' => '1 x sehari  1 tube  (OBAT LUAR)',
    'code' => NULL,
    'is_active' => true,
  ),
  111 => 
  array (
    'name' => '2 X sehari  1 sachet sebelum makan',
    'code' => NULL,
    'is_active' => true,
  ),
  112 => 
  array (
    'name' => '2x1',
    'code' => NULL,
    'is_active' => true,
  ),
  113 => 
  array (
    'name' => '3x1',
    'code' => NULL,
    'is_active' => true,
  ),
  114 => 
  array (
    'name' => '1 X SEHARI 1 tetes',
    'code' => NULL,
    'is_active' => true,
  ),
  115 => 
  array (
    'name' => '3 x sehari  1 kapsul',
    'code' => NULL,
    'is_active' => true,
  ),
  116 => 
  array (
    'name' => '2 x sehari  1 sachet  ( Sebelum Makan )',
    'code' => NULL,
    'is_active' => true,
  ),
  117 => 
  array (
    'name' => '3 x sehari  1 tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  118 => 
  array (
    'name' => '1 x sehari  1 tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  119 => 
  array (
    'name' => '1 x sehari  1 tablet  (obat sakit tulang)',
    'code' => NULL,
    'is_active' => true,
  ),
  120 => 
  array (
    'name' => '1 X sehari  1 tablet  (PAGI)',
    'code' => NULL,
    'is_active' => true,
  ),
  121 => 
  array (
    'name' => '1 x sehari  1 sendok obat  (sesudah makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  122 => 
  array (
    'name' => '1 X1',
    'code' => NULL,
    'is_active' => true,
  ),
  123 => 
  array (
    'name' => '3 x sehari 1 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  124 => 
  array (
    'name' => '1 X sehari 2 tetes',
    'code' => NULL,
    'is_active' => true,
  ),
  125 => 
  array (
    'name' => '1 x',
    'code' => NULL,
    'is_active' => true,
  ),
  126 => 
  array (
    'name' => '2 X sehari  1 kapsul  (sesudah makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  127 => 
  array (
    'name' => 'OBAT LUAR',
    'code' => NULL,
    'is_active' => true,
  ),
  128 => 
  array (
    'name' => '3 x sehari  0,9 cc  (bila demam)',
    'code' => NULL,
    'is_active' => true,
  ),
  129 => 
  array (
    'name' => '3 x sehari  0,8 cc   (bila demam)',
    'code' => NULL,
    'is_active' => true,
  ),
  130 => 
  array (
    'name' => '3 x sehari  0,7 cc (bila demam)',
    'code' => NULL,
    'is_active' => true,
  ),
  131 => 
  array (
    'name' => '1 x sehari  3/4 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  132 => 
  array (
    'name' => '2 x sehari 1 sachet  (sesudah makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  133 => 
  array (
    'name' => '2x400 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  134 => 
  array (
    'name' => '3 X 3/4 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  135 => 
  array (
    'name' => '3 x sehari1 tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  136 => 
  array (
    'name' => 'pro inj',
    'code' => NULL,
    'is_active' => true,
  ),
  137 => 
  array (
    'name' => '1 X SEHARI 1 SACHET',
    'code' => NULL,
    'is_active' => true,
  ),
  138 => 
  array (
    'name' => '1 X 0,4 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  139 => 
  array (
    'name' => '3 x sehari 1 1/2  sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  140 => 
  array (
    'name' => '2 x sehari  1 sendok obat  (sebelum makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  141 => 
  array (
    'name' => '2 X sehari  0,35 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  142 => 
  array (
    'name' => '3  X SEHARI  3/4 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  143 => 
  array (
    'name' => '1 x sehari  1 CC  (SEBELUM MAKAN)',
    'code' => NULL,
    'is_active' => true,
  ),
  144 => 
  array (
    'name' => '1 X SEHARI 1 SACHET    (SEBELUM MAKAN)',
    'code' => NULL,
    'is_active' => true,
  ),
  145 => 
  array (
    'name' => '1 X SEHARI  1 KAPSUL',
    'code' => NULL,
    'is_active' => true,
  ),
  146 => 
  array (
    'name' => '1 kali 1',
    'code' => NULL,
    'is_active' => true,
  ),
  147 => 
  array (
    'name' => '3 sehari 1 tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  148 => 
  array (
    'name' => '2 X sehari 1 bungkus',
    'code' => NULL,
    'is_active' => true,
  ),
  149 => 
  array (
    'name' => '3 kali 1',
    'code' => NULL,
    'is_active' => true,
  ),
  150 => 
  array (
    'name' => '3 x sehari 0,6 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  151 => 
  array (
    'name' => '3 X 0,7 CC ',
    'code' => NULL,
    'is_active' => true,
  ),
  152 => 
  array (
    'name' => '1 X 2 TABLET',
    'code' => NULL,
    'is_active' => true,
  ),
  153 => 
  array (
    'name' => '3  X SEHARI  1 BUNGKUS',
    'code' => NULL,
    'is_active' => true,
  ),
  154 => 
  array (
    'name' => '2 X sehari  3/4 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  155 => 
  array (
    'name' => '1 X SEHARI 1 SENDOK OBAT  (SEBELUM MAKAN)',
    'code' => NULL,
    'is_active' => true,
  ),
  156 => 
  array (
    'name' => 'OLES TIPIS-TIPIS',
    'code' => NULL,
    'is_active' => true,
  ),
  157 => 
  array (
    'name' => '2 X sehari 0,35 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  158 => 
  array (
    'name' => '2 x sehari 1/2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  159 => 
  array (
    'name' => '3 x sehari   1 1/2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  160 => 
  array (
    'name' => '1 X SEHARI  3 tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  161 => 
  array (
    'name' => '2 x sehari  1 sachet  (sebelum makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  162 => 
  array (
    'name' => '2 x sehari  1 1/2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  163 => 
  array (
    'name' => '2 X sehari  1 kapsul  (sebelum makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  164 => 
  array (
    'name' => '2 X sehari  2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  165 => 
  array (
    'name' => '3 x sehari  3/ 4 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  166 => 
  array (
    'name' => '1 x sehari  1 sendok obat  (sebelum makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  167 => 
  array (
    'name' => '2 x sehari  1/2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  168 => 
  array (
    'name' => '5 x sehari  1 bungkus  ',
    'code' => NULL,
    'is_active' => true,
  ),
  169 => 
  array (
    'name' => '3 x sehari  1 1/2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  170 => 
  array (
    'name' => '2 x sehari 1 sachet  (SEBELUM MAKAN)',
    'code' => NULL,
    'is_active' => true,
  ),
  171 => 
  array (
    'name' => '3 x sehari  1 1/4 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  172 => 
  array (
    'name' => '1 X SEHARI  1 SENDOK OBAT (SEBELUM MAKAN)',
    'code' => NULL,
    'is_active' => true,
  ),
  173 => 
  array (
    'name' => '2 x sehari  1 sachet  (SEBELUM MAKAN )',
    'code' => NULL,
    'is_active' => true,
  ),
  174 => 
  array (
    'name' => '5 X 1 BUNGKUS',
    'code' => NULL,
    'is_active' => true,
  ),
  175 => 
  array (
    'name' => '1 X 1/2 TABLET',
    'code' => NULL,
    'is_active' => true,
  ),
  176 => 
  array (
    'name' => '1 X 1 KAPSUL (PAGI)',
    'code' => NULL,
    'is_active' => true,
  ),
  177 => 
  array (
    'name' => '1 X 1 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  178 => 
  array (
    'name' => '3x1 sendok',
    'code' => NULL,
    'is_active' => true,
  ),
  179 => 
  array (
    'name' => '2 X sehari 3/4 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  180 => 
  array (
    'name' => '3 x sehari  2 sendok',
    'code' => NULL,
    'is_active' => true,
  ),
  181 => 
  array (
    'name' => '2 X sehari  0,25 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  182 => 
  array (
    'name' => '2 X sehari 1 kapsul (sebelum makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  183 => 
  array (
    'name' => '2 x sehari 1 bungkus (sebelum makan )',
    'code' => NULL,
    'is_active' => true,
  ),
  184 => 
  array (
    'name' => '2 X 1 BUNGKUS ( sebelum makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  185 => 
  array (
    'name' => '2 X 1 KAPSUL ( sebelum makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  186 => 
  array (
    'name' => '2 X1 BUNGKUS ( sebelum makan)',
    'code' => NULL,
    'is_active' => true,
  ),
  187 => 
  array (
    'name' => '1 X1 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  188 => 
  array (
    'name' => '2 X sehari  1 BUNGKUS',
    'code' => NULL,
    'is_active' => true,
  ),
  189 => 
  array (
    'name' => '1 X SEHARI 1 TABLET  (PAGI)',
    'code' => NULL,
    'is_active' => true,
  ),
  190 => 
  array (
    'name' => '1 X SEHARI  1 TABLET (SIANG)',
    'code' => NULL,
    'is_active' => true,
  ),
  191 => 
  array (
    'name' => '3 X 1 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  192 => 
  array (
    'name' => '3  X 0,5 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  193 => 
  array (
    'name' => '2 X 400 mg',
    'code' => NULL,
    'is_active' => true,
  ),
  194 => 
  array (
    'name' => 'imm',
    'code' => NULL,
    'is_active' => true,
  ),
  195 => 
  array (
    'name' => ' 1 x 1',
    'code' => NULL,
    'is_active' => true,
  ),
  196 => 
  array (
    'name' => '1 X1 ML',
    'code' => NULL,
    'is_active' => true,
  ),
  197 => 
  array (
    'name' => '3 X 0,3 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  198 => 
  array (
    'name' => '3 x 2 tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  199 => 
  array (
    'name' => '1 x 1 ml ',
    'code' => NULL,
    'is_active' => true,
  ),
  200 => 
  array (
    'name' => '3 X 0,5 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  201 => 
  array (
    'name' => '7x 20 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  202 => 
  array (
    'name' => '2 X 1/2 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  203 => 
  array (
    'name' => '3 x 2 SENDOK TEH',
    'code' => NULL,
    'is_active' => true,
  ),
  204 => 
  array (
    'name' => '1 X 0,3 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  205 => 
  array (
    'name' => '3 x 2 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  206 => 
  array (
    'name' => '3 x sehari  1/2 sendok obat  (bila demam)',
    'code' => NULL,
    'is_active' => true,
  ),
  207 => 
  array (
    'name' => '2 X sehari 0,25 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  208 => 
  array (
    'name' => '1 X 3 TETES',
    'code' => NULL,
    'is_active' => true,
  ),
  209 => 
  array (
    'name' => '3 X 1 SACHET',
    'code' => NULL,
    'is_active' => true,
  ),
  210 => 
  array (
    'name' => '2 X 0,3 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  211 => 
  array (
    'name' => '2 x 2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  212 => 
  array (
    'name' => ' 3 X SEHARI 1 BUNGKUS',
    'code' => NULL,
    'is_active' => true,
  ),
  213 => 
  array (
    'name' => '3 x sehari 6 ml (BILA DEMAM)',
    'code' => NULL,
    'is_active' => true,
  ),
  214 => 
  array (
    'name' => '2 X SEHARI  1 TABLET',
    'code' => NULL,
    'is_active' => true,
  ),
  215 => 
  array (
    'name' => '2 X 1 1/2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  216 => 
  array (
    'name' => '3 x 1 SENDOK MAKAN',
    'code' => NULL,
    'is_active' => true,
  ),
  217 => 
  array (
    'name' => '3 X1 TABLET',
    'code' => NULL,
    'is_active' => true,
  ),
  218 => 
  array (
    'name' => '3 x sehari  1/2 sendok obat (Pencegah Kejang)',
    'code' => NULL,
    'is_active' => true,
  ),
  219 => 
  array (
    'name' => '3 x sehari 1 sendok obat (Bila Demam)',
    'code' => NULL,
    'is_active' => true,
  ),
  220 => 
  array (
    'name' => '2 X sehari  0,3 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  221 => 
  array (
    'name' => '1 X 3/4 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  222 => 
  array (
    'name' => '1 X SEHARI 1 TABLET (Siang)',
    'code' => NULL,
    'is_active' => true,
  ),
  223 => 
  array (
    'name' => '2 X sehari 1 tablet  (Pagi dan Malam)',
    'code' => NULL,
    'is_active' => true,
  ),
  224 => 
  array (
    'name' => '1 X SEHARI  1 tablet (Malam)',
    'code' => NULL,
    'is_active' => true,
  ),
  225 => 
  array (
    'name' => '3  X SEHARI 1 BUNGKUS',
    'code' => NULL,
    'is_active' => true,
  ),
  226 => 
  array (
    'name' => '3 x sehari  1 1/2 sendok obat (BILA DEMAM)',
    'code' => NULL,
    'is_active' => true,
  ),
  227 => 
  array (
    'name' => '3 X  1 BUNGKUS ',
    'code' => NULL,
    'is_active' => true,
  ),
  228 => 
  array (
    'name' => '2 X sehari 0,3 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  229 => 
  array (
    'name' => '1 X 0,8 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  230 => 
  array (
    'name' => '2 X sehari1 sachet',
    'code' => NULL,
    'is_active' => true,
  ),
  231 => 
  array (
    'name' => '2 X sehari 2 1/2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  232 => 
  array (
    'name' => '3 X 1/2 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  233 => 
  array (
    'name' => '2 X sehari 1 kapsul sebelum makan',
    'code' => NULL,
    'is_active' => true,
  ),
  234 => 
  array (
    'name' => '2 X 0,25 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  235 => 
  array (
    'name' => '2 X1 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  236 => 
  array (
    'name' => '2 X1 SACHET',
    'code' => NULL,
    'is_active' => true,
  ),
  237 => 
  array (
    'name' => '2 x 2  cc',
    'code' => NULL,
    'is_active' => true,
  ),
  238 => 
  array (
    'name' => 'i x sehari 1 tube lewat rectal',
    'code' => NULL,
    'is_active' => true,
  ),
  239 => 
  array (
    'name' => '2 X sehari 2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  240 => 
  array (
    'name' => '1 X sehari 1 tablet (Malam) jam 10.00',
    'code' => NULL,
    'is_active' => true,
  ),
  241 => 
  array (
    'name' => '1 X SEHARI 1 tube melalui rectal',
    'code' => NULL,
    'is_active' => true,
  ),
  242 => 
  array (
    'name' => '1 X SEHARI 1 TABLET  (MALAM)',
    'code' => NULL,
    'is_active' => true,
  ),
  243 => 
  array (
    'name' => '3 x sehari 1 sendok obat (5 ml)',
    'code' => NULL,
    'is_active' => true,
  ),
  244 => 
  array (
    'name' => '1 x sehari tablet',
    'code' => NULL,
    'is_active' => true,
  ),
  245 => 
  array (
    'name' => '3 X 0,6 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  246 => 
  array (
    'name' => '2 X 0,2 cc',
    'code' => NULL,
    'is_active' => true,
  ),
  247 => 
  array (
    'name' => '2 X sehari 1 capsul (sebelum makan) ',
    'code' => NULL,
    'is_active' => true,
  ),
  248 => 
  array (
    'name' => '3 x sehari 1 tetes ',
    'code' => NULL,
    'is_active' => true,
  ),
  249 => 
  array (
    'name' => '3 X 1 1/4 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  250 => 
  array (
    'name' => '3 X1 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  251 => 
  array (
    'name' => '3 X 0,4 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  252 => 
  array (
    'name' => '3 X 0,8 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  253 => 
  array (
    'name' => '3 x sehari 2 1/2 sendok obat (12,5 ml)',
    'code' => NULL,
    'is_active' => true,
  ),
  254 => 
  array (
    'name' => 'PENCAMPUR OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  255 => 
  array (
    'name' => '1 X SEHARI UNTUK NEBUL',
    'code' => NULL,
    'is_active' => true,
  ),
  256 => 
  array (
    'name' => '3 x sehari 1 1/2 SENDOK OBAT (BILA DEMAM)',
    'code' => NULL,
    'is_active' => true,
  ),
  257 => 
  array (
    'name' => '3 x sehari 1 sendok obat (Pencegah Kejang)',
    'code' => NULL,
    'is_active' => true,
  ),
  258 => 
  array (
    'name' => '3 x sehari 1 1/4 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  259 => 
  array (
    'name' => '3 x sehari 2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  260 => 
  array (
    'name' => '3 x sehari 1/2 sendok obat',
    'code' => NULL,
    'is_active' => true,
  ),
  261 => 
  array (
    'name' => '2 x 2 1/2 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  262 => 
  array (
    'name' => '3 x sehari 1 kapsul (Obat Sakit Kepala)',
    'code' => NULL,
    'is_active' => true,
  ),
  263 => 
  array (
    'name' => '3 x sehari 1 SENDOK MAKAN (1/2 JAM SEBELUM MAKAN)',
    'code' => NULL,
    'is_active' => true,
  ),
  264 => 
  array (
    'name' => '3 X sehari 1 tablet ( TIAP 8 JAM)',
    'code' => NULL,
    'is_active' => true,
  ),
  265 => 
  array (
    'name' => '3 x sehari 0,9 CC (BILA DEMAM)',
    'code' => NULL,
    'is_active' => true,
  ),
  266 => 
  array (
    'name' => '4 x sehari 1 tetes pada mata yang sakit',
    'code' => NULL,
    'is_active' => true,
  ),
  267 => 
  array (
    'name' => '2x90 mg/IM',
    'code' => NULL,
    'is_active' => true,
  ),
  268 => 
  array (
    'name' => '1 X SEHARI 0,3 CC',
    'code' => NULL,
    'is_active' => true,
  ),
  269 => 
  array (
    'name' => '1 X 1 TABLET (pagi)',
    'code' => NULL,
    'is_active' => true,
  ),
  270 => 
  array (
    'name' => '2 X SEHARI 1 1/2 SENDOK OBAT',
    'code' => NULL,
    'is_active' => true,
  ),
  271 => 
  array (
    'name' => '3  X SEHARI 1 SENDOK OBAT (BILA DEMAM)',
    'code' => NULL,
    'is_active' => true,
  ),
  272 => 
  array (
    'name' => '3 x SEHARI 1/2 SENDOK OBAT (PENCEGAH KEJANG)',
    'code' => NULL,
    'is_active' => true,
  ),
  273 => 
  array (
    'name' => '1 x 22',
    'code' => NULL,
    'is_active' => true,
  ),
  274 => 
  array (
    'name' => '1 x 45',
    'code' => NULL,
    'is_active' => true,
  ),
  275 => 
  array (
    'name' => '',
    'code' => NULL,
    'is_active' => true,
  ),
  276 => 
  array (
    'name' => '1 kali 1 hari',
    'code' => NULL,
    'is_active' => true,
  ),
  277 => 
  array (
    'name' => 'babnabab',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('dosage_instructions')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}