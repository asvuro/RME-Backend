<?php

namespace Modules\GeneralManufacturer\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralManufacturerDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 39).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => '3M',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'Aaron',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Abbot Vascular',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Abbot/Guidant',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Abbott',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Abdi Farma',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'ABLE',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'ABN',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'Accurate',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'Actavis',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => 'Actavis, PT',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => 'Adam\'s',
    'code' => NULL,
    'is_active' => true,
  ),
  12 => 
  array (
    'name' => 'Aesculap',
    'code' => NULL,
    'is_active' => true,
  ),
  13 => 
  array (
    'name' => 'Airway',
    'code' => NULL,
    'is_active' => true,
  ),
  14 => 
  array (
    'name' => 'Alexa',
    'code' => NULL,
    'is_active' => true,
  ),
  15 => 
  array (
    'name' => 'Alpharma',
    'code' => NULL,
    'is_active' => true,
  ),
  16 => 
  array (
    'name' => 'Altech',
    'code' => NULL,
    'is_active' => true,
  ),
  17 => 
  array (
    'name' => 'AMBU',
    'code' => NULL,
    'is_active' => true,
  ),
  18 => 
  array (
    'name' => 'AMS/Hospitech',
    'code' => NULL,
    'is_active' => true,
  ),
  19 => 
  array (
    'name' => 'Ansell',
    'code' => NULL,
    'is_active' => true,
  ),
  20 => 
  array (
    'name' => 'Ansell-Gammex',
    'code' => NULL,
    'is_active' => true,
  ),
  21 => 
  array (
    'name' => 'Anugrahmitra Selaras',
    'code' => NULL,
    'is_active' => true,
  ),
  22 => 
  array (
    'name' => 'Apex',
    'code' => NULL,
    'is_active' => true,
  ),
  23 => 
  array (
    'name' => 'ARBO',
    'code' => NULL,
    'is_active' => true,
  ),
  24 => 
  array (
    'name' => 'arbu',
    'code' => NULL,
    'is_active' => true,
  ),
  25 => 
  array (
    'name' => 'Ardeni',
    'code' => NULL,
    'is_active' => true,
  ),
  26 => 
  array (
    'name' => 'Argon',
    'code' => NULL,
    'is_active' => true,
  ),
  27 => 
  array (
    'name' => 'Argon Medical',
    'code' => NULL,
    'is_active' => true,
  ),
  28 => 
  array (
    'name' => 'Armoxindo',
    'code' => NULL,
    'is_active' => true,
  ),
  29 => 
  array (
    'name' => 'Armoxindo Farma',
    'code' => NULL,
    'is_active' => true,
  ),
  30 => 
  array (
    'name' => 'Arrow',
    'code' => NULL,
    'is_active' => true,
  ),
  31 => 
  array (
    'name' => 'Arrow Teleflex',
    'code' => NULL,
    'is_active' => true,
  ),
  32 => 
  array (
    'name' => 'Assistant',
    'code' => NULL,
    'is_active' => true,
  ),
  33 => 
  array (
    'name' => 'Astellas',
    'code' => NULL,
    'is_active' => true,
  ),
  34 => 
  array (
    'name' => 'Astra Zaneca',
    'code' => NULL,
    'is_active' => true,
  ),
  35 => 
  array (
    'name' => 'Astra Zeneca',
    'code' => NULL,
    'is_active' => true,
  ),
  36 => 
  array (
    'name' => 'Aventis',
    'code' => NULL,
    'is_active' => true,
  ),
  37 => 
  array (
    'name' => 'B. Braun',
    'code' => NULL,
    'is_active' => true,
  ),
  38 => 
  array (
    'name' => 'Bayer',
    'code' => NULL,
    'is_active' => true,
  ),
  39 => 
  array (
    'name' => 'BB',
    'code' => NULL,
    'is_active' => true,
  ),
  40 => 
  array (
    'name' => 'B-Braun',
    'code' => NULL,
    'is_active' => true,
  ),
  41 => 
  array (
    'name' => 'BD',
    'code' => NULL,
    'is_active' => true,
  ),
  42 => 
  array (
    'name' => 'Beaufour Ipsen',
    'code' => NULL,
    'is_active' => true,
  ),
  43 => 
  array (
    'name' => 'Bernofarm',
    'code' => NULL,
    'is_active' => true,
  ),
  44 => 
  array (
    'name' => 'Besmed',
    'code' => NULL,
    'is_active' => true,
  ),
  45 => 
  array (
    'name' => 'Besmed/Gea',
    'code' => NULL,
    'is_active' => true,
  ),
  46 => 
  array (
    'name' => 'Binda',
    'code' => NULL,
    'is_active' => true,
  ),
  47 => 
  array (
    'name' => 'Bio Farma',
    'code' => NULL,
    'is_active' => true,
  ),
  48 => 
  array (
    'name' => 'Bio Gaia',
    'code' => NULL,
    'is_active' => true,
  ),
  49 => 
  array (
    'name' => 'Biolife',
    'code' => NULL,
    'is_active' => true,
  ),
  50 => 
  array (
    'name' => 'Bionet',
    'code' => NULL,
    'is_active' => true,
  ),
  51 => 
  array (
    'name' => 'Bioteq',
    'code' => NULL,
    'is_active' => true,
  ),
  52 => 
  array (
    'name' => 'Blue Sensor',
    'code' => NULL,
    'is_active' => true,
  ),
  53 => 
  array (
    'name' => 'Boehringer',
    'code' => NULL,
    'is_active' => true,
  ),
  54 => 
  array (
    'name' => 'Brataco',
    'code' => NULL,
    'is_active' => true,
  ),
  55 => 
  array (
    'name' => 'BSN',
    'code' => NULL,
    'is_active' => true,
  ),
  56 => 
  array (
    'name' => 'Bu Kwang Medical,PT',
    'code' => NULL,
    'is_active' => true,
  ),
  57 => 
  array (
    'name' => 'Bunga Biru',
    'code' => NULL,
    'is_active' => true,
  ),
  58 => 
  array (
    'name' => 'Burdick',
    'code' => NULL,
    'is_active' => true,
  ),
  59 => 
  array (
    'name' => 'Burrough welcome',
    'code' => NULL,
    'is_active' => true,
  ),
  60 => 
  array (
    'name' => 'Camex',
    'code' => NULL,
    'is_active' => true,
  ),
  61 => 
  array (
    'name' => 'Cardicare',
    'code' => NULL,
    'is_active' => true,
  ),
  62 => 
  array (
    'name' => 'Cardio Care',
    'code' => NULL,
    'is_active' => true,
  ),
  63 => 
  array (
    'name' => 'Cardiosens',
    'code' => NULL,
    'is_active' => true,
  ),
  64 => 
  array (
    'name' => 'Carpule',
    'code' => NULL,
    'is_active' => true,
  ),
  65 => 
  array (
    'name' => 'Cendo',
    'code' => NULL,
    'is_active' => true,
  ),
  66 => 
  array (
    'name' => 'Chabbra',
    'code' => NULL,
    'is_active' => true,
  ),
  67 => 
  array (
    'name' => 'Chauvin',
    'code' => NULL,
    'is_active' => true,
  ),
  68 => 
  array (
    'name' => 'Claxo',
    'code' => NULL,
    'is_active' => true,
  ),
  69 => 
  array (
    'name' => 'Combiphar',
    'code' => NULL,
    'is_active' => true,
  ),
  70 => 
  array (
    'name' => 'Conic Vascular',
    'code' => NULL,
    'is_active' => true,
  ),
  71 => 
  array (
    'name' => 'Conmed',
    'code' => NULL,
    'is_active' => true,
  ),
  72 => 
  array (
    'name' => 'Cook Incorporated',
    'code' => NULL,
    'is_active' => true,
  ),
  73 => 
  array (
    'name' => 'Cordis',
    'code' => NULL,
    'is_active' => true,
  ),
  74 => 
  array (
    'name' => 'Coronet Crown',
    'code' => NULL,
    'is_active' => true,
  ),
  75 => 
  array (
    'name' => 'Corsa',
    'code' => NULL,
    'is_active' => true,
  ),
  76 => 
  array (
    'name' => 'Covidien Ireland Limited',
    'code' => NULL,
    'is_active' => true,
  ),
  77 => 
  array (
    'name' => 'CURA',
    'code' => NULL,
    'is_active' => true,
  ),
  78 => 
  array (
    'name' => 'CV. De\'monabe Jaya',
    'code' => NULL,
    'is_active' => true,
  ),
  79 => 
  array (
    'name' => 'Dankos',
    'code' => NULL,
    'is_active' => true,
  ),
  80 => 
  array (
    'name' => 'Dansac',
    'code' => NULL,
    'is_active' => true,
  ),
  81 => 
  array (
    'name' => 'Darya Varia',
    'code' => NULL,
    'is_active' => true,
  ),
  82 => 
  array (
    'name' => 'Dasa Esa Farma',
    'code' => NULL,
    'is_active' => true,
  ),
  83 => 
  array (
    'name' => 'DEW',
    'code' => NULL,
    'is_active' => true,
  ),
  84 => 
  array (
    'name' => 'Dexa Medica',
    'code' => NULL,
    'is_active' => true,
  ),
  85 => 
  array (
    'name' => 'Diamond',
    'code' => NULL,
    'is_active' => true,
  ),
  86 => 
  array (
    'name' => 'DIPA HUSADA',
    'code' => NULL,
    'is_active' => true,
  ),
  87 => 
  array (
    'name' => 'DIPA Pharmalab',
    'code' => NULL,
    'is_active' => true,
  ),
  88 => 
  array (
    'name' => 'DirJen Binfar & Alkes',
    'code' => NULL,
    'is_active' => true,
  ),
  89 => 
  array (
    'name' => 'Dr J',
    'code' => NULL,
    'is_active' => true,
  ),
  90 => 
  array (
    'name' => 'Durasorb',
    'code' => NULL,
    'is_active' => true,
  ),
  91 => 
  array (
    'name' => 'Dwipa',
    'code' => NULL,
    'is_active' => true,
  ),
  92 => 
  array (
    'name' => 'Dynek Sutures',
    'code' => NULL,
    'is_active' => true,
  ),
  93 => 
  array (
    'name' => 'Eisai',
    'code' => NULL,
    'is_active' => true,
  ),
  94 => 
  array (
    'name' => 'Eli Lily',
    'code' => NULL,
    'is_active' => true,
  ),
  95 => 
  array (
    'name' => 'Errita farma',
    'code' => NULL,
    'is_active' => true,
  ),
  96 => 
  array (
    'name' => 'Escolab',
    'code' => NULL,
    'is_active' => true,
  ),
  97 => 
  array (
    'name' => 'Ethica',
    'code' => NULL,
    'is_active' => true,
  ),
  98 => 
  array (
    'name' => 'Ethicon',
    'code' => NULL,
    'is_active' => true,
  ),
  99 => 
  array (
    'name' => 'Fahrenheit',
    'code' => NULL,
    'is_active' => true,
  ),
  100 => 
  array (
    'name' => 'Fahrenheit ',
    'code' => NULL,
    'is_active' => true,
  ),
  101 => 
  array (
    'name' => 'Falcon',
    'code' => NULL,
    'is_active' => true,
  ),
  102 => 
  array (
    'name' => 'Ferron',
    'code' => NULL,
    'is_active' => true,
  ),
  103 => 
  array (
    'name' => 'Fixotherm',
    'code' => NULL,
    'is_active' => true,
  ),
  104 => 
  array (
    'name' => 'Fresenius',
    'code' => NULL,
    'is_active' => true,
  ),
  105 => 
  array (
    'name' => 'FUJI',
    'code' => NULL,
    'is_active' => true,
  ),
  106 => 
  array (
    'name' => 'Fukuda',
    'code' => NULL,
    'is_active' => true,
  ),
  107 => 
  array (
    'name' => 'Futura',
    'code' => NULL,
    'is_active' => true,
  ),
  108 => 
  array (
    'name' => 'G. Medik',
    'code' => NULL,
    'is_active' => true,
  ),
  109 => 
  array (
    'name' => 'Galenium',
    'code' => NULL,
    'is_active' => true,
  ),
  110 => 
  array (
    'name' => 'Gammex',
    'code' => NULL,
    'is_active' => true,
  ),
  111 => 
  array (
    'name' => 'Gea',
    'code' => NULL,
    'is_active' => true,
  ),
  112 => 
  array (
    'name' => 'Germany',
    'code' => NULL,
    'is_active' => true,
  ),
  113 => 
  array (
    'name' => 'Glaxo',
    'code' => NULL,
    'is_active' => true,
  ),
  114 => 
  array (
    'name' => 'Goretex',
    'code' => NULL,
    'is_active' => true,
  ),
  115 => 
  array (
    'name' => 'Gracia Pharmindo',
    'code' => NULL,
    'is_active' => true,
  ),
  116 => 
  array (
    'name' => 'GS-Jaya',
    'code' => NULL,
    'is_active' => true,
  ),
  117 => 
  array (
    'name' => 'GST Comp',
    'code' => NULL,
    'is_active' => true,
  ),
  118 => 
  array (
    'name' => 'Guardian',
    'code' => NULL,
    'is_active' => true,
  ),
  119 => 
  array (
    'name' => 'GW',
    'code' => NULL,
    'is_active' => true,
  ),
  120 => 
  array (
    'name' => 'Havox',
    'code' => NULL,
    'is_active' => true,
  ),
  121 => 
  array (
    'name' => 'Healer',
    'code' => NULL,
    'is_active' => true,
  ),
  122 => 
  array (
    'name' => 'Health Care',
    'code' => NULL,
    'is_active' => true,
  ),
  123 => 
  array (
    'name' => 'Heuer',
    'code' => NULL,
    'is_active' => true,
  ),
  124 => 
  array (
    'name' => 'Hexa',
    'code' => NULL,
    'is_active' => true,
  ),
  125 => 
  array (
    'name' => 'HK',
    'code' => NULL,
    'is_active' => true,
  ),
  126 => 
  array (
    'name' => 'Hospitech',
    'code' => NULL,
    'is_active' => false,
  ),
  127 => 
  array (
    'name' => 'Hovid',
    'code' => NULL,
    'is_active' => true,
  ),
  128 => 
  array (
    'name' => 'Huntington',
    'code' => NULL,
    'is_active' => true,
  ),
  129 => 
  array (
    'name' => 'IBS',
    'code' => NULL,
    'is_active' => true,
  ),
  130 => 
  array (
    'name' => 'Ikapharmindo',
    'code' => NULL,
    'is_active' => true,
  ),
  131 => 
  array (
    'name' => 'India',
    'code' => NULL,
    'is_active' => true,
  ),
  132 => 
  array (
    'name' => 'Indo Farma',
    'code' => NULL,
    'is_active' => true,
  ),
  133 => 
  array (
    'name' => 'Injekt',
    'code' => NULL,
    'is_active' => true,
  ),
  134 => 
  array (
    'name' => 'Inmark',
    'code' => NULL,
    'is_active' => true,
  ),
  135 => 
  array (
    'name' => 'Inspire MD',
    'code' => NULL,
    'is_active' => true,
  ),
  136 => 
  array (
    'name' => 'Integra',
    'code' => NULL,
    'is_active' => true,
  ),
  137 => 
  array (
    'name' => 'Interbat',
    'code' => NULL,
    'is_active' => true,
  ),
  138 => 
  array (
    'name' => 'Interbat ',
    'code' => NULL,
    'is_active' => true,
  ),
  139 => 
  array (
    'name' => 'Inusko',
    'code' => NULL,
    'is_active' => true,
  ),
  140 => 
  array (
    'name' => 'Ionomed',
    'code' => NULL,
    'is_active' => true,
  ),
  141 => 
  array (
    'name' => 'ITC',
    'code' => NULL,
    'is_active' => true,
  ),
  142 => 
  array (
    'name' => 'J&J',
    'code' => NULL,
    'is_active' => true,
  ),
  143 => 
  array (
    'name' => 'Janssen',
    'code' => NULL,
    'is_active' => true,
  ),
  144 => 
  array (
    'name' => 'JMS',
    'code' => NULL,
    'is_active' => true,
  ),
  145 => 
  array (
    'name' => 'Kalbe Farma',
    'code' => NULL,
    'is_active' => true,
  ),
  146 => 
  array (
    'name' => 'Kapsulindo',
    'code' => NULL,
    'is_active' => true,
  ),
  147 => 
  array (
    'name' => 'Kasa Husada',
    'code' => NULL,
    'is_active' => true,
  ),
  148 => 
  array (
    'name' => 'Kendal Arbo',
    'code' => NULL,
    'is_active' => true,
  ),
  149 => 
  array (
    'name' => 'Kimia Farma',
    'code' => NULL,
    'is_active' => true,
  ),
  150 => 
  array (
    'name' => 'KODAK',
    'code' => NULL,
    'is_active' => true,
  ),
  151 => 
  array (
    'name' => 'Koo Medical Equipment Co. Ltd',
    'code' => NULL,
    'is_active' => true,
  ),
  152 => 
  array (
    'name' => 'Kromopan',
    'code' => NULL,
    'is_active' => true,
  ),
  153 => 
  array (
    'name' => 'Kyowa',
    'code' => NULL,
    'is_active' => true,
  ),
  154 => 
  array (
    'name' => 'L&R',
    'code' => NULL,
    'is_active' => true,
  ),
  155 => 
  array (
    'name' => 'Landson',
    'code' => NULL,
    'is_active' => true,
  ),
  156 => 
  array (
    'name' => 'Lapi',
    'code' => NULL,
    'is_active' => true,
  ),
  157 => 
  array (
    'name' => 'Lederle',
    'code' => NULL,
    'is_active' => true,
  ),
  158 => 
  array (
    'name' => 'Liferesources',
    'code' => NULL,
    'is_active' => true,
  ),
  159 => 
  array (
    'name' => 'Lina Denmark',
    'code' => NULL,
    'is_active' => true,
  ),
  160 => 
  array (
    'name' => 'lokal',
    'code' => NULL,
    'is_active' => true,
  ),
  161 => 
  array (
    'name' => 'Lovell',
    'code' => NULL,
    'is_active' => true,
  ),
  162 => 
  array (
    'name' => 'Mahakam Beta Farma',
    'code' => NULL,
    'is_active' => true,
  ),
  163 => 
  array (
    'name' => 'Makida',
    'code' => NULL,
    'is_active' => true,
  ),
  164 => 
  array (
    'name' => 'Mammi',
    'code' => NULL,
    'is_active' => true,
  ),
  165 => 
  array (
    'name' => 'Mani',
    'code' => NULL,
    'is_active' => true,
  ),
  166 => 
  array (
    'name' => 'MAS',
    'code' => NULL,
    'is_active' => true,
  ),
  167 => 
  array (
    'name' => 'Maxitex',
    'code' => NULL,
    'is_active' => true,
  ),
  168 => 
  array (
    'name' => 'Maxter',
    'code' => NULL,
    'is_active' => true,
  ),
  169 => 
  array (
    'name' => 'Medex',
    'code' => NULL,
    'is_active' => true,
  ),
  170 => 
  array (
    'name' => 'Medgyn',
    'code' => NULL,
    'is_active' => true,
  ),
  171 => 
  array (
    'name' => 'Medic',
    'code' => NULL,
    'is_active' => true,
  ),
  172 => 
  array (
    'name' => 'Medifarma',
    'code' => NULL,
    'is_active' => true,
  ),
  173 => 
  array (
    'name' => 'Medigloves',
    'code' => NULL,
    'is_active' => true,
  ),
  174 => 
  array (
    'name' => 'Meditama',
    'code' => NULL,
    'is_active' => true,
  ),
  175 => 
  array (
    'name' => 'Medtronic',
    'code' => NULL,
    'is_active' => true,
  ),
  176 => 
  array (
    'name' => 'Meiji',
    'code' => NULL,
    'is_active' => true,
  ),
  177 => 
  array (
    'name' => 'Meprofarma',
    'code' => NULL,
    'is_active' => true,
  ),
  178 => 
  array (
    'name' => 'Merc',
    'code' => NULL,
    'is_active' => true,
  ),
  179 => 
  array (
    'name' => 'Merit',
    'code' => NULL,
    'is_active' => true,
  ),
  180 => 
  array (
    'name' => 'Merit Medical',
    'code' => NULL,
    'is_active' => true,
  ),
  181 => 
  array (
    'name' => 'Mersi Farma',
    'code' => NULL,
    'is_active' => true,
  ),
  182 => 
  array (
    'name' => 'Metz',
    'code' => NULL,
    'is_active' => true,
  ),
  183 => 
  array (
    'name' => 'Mexpharm',
    'code' => NULL,
    'is_active' => true,
  ),
  184 => 
  array (
    'name' => 'Minato',
    'code' => NULL,
    'is_active' => true,
  ),
  185 => 
  array (
    'name' => 'Mitsubishi',
    'code' => NULL,
    'is_active' => true,
  ),
  186 => 
  array (
    'name' => 'Mitsubisi',
    'code' => NULL,
    'is_active' => true,
  ),
  187 => 
  array (
    'name' => 'MM',
    'code' => NULL,
    'is_active' => true,
  ),
  188 => 
  array (
    'name' => 'MPM',
    'code' => NULL,
    'is_active' => true,
  ),
  189 => 
  array (
    'name' => 'MSD Frosst Iberica',
    'code' => NULL,
    'is_active' => true,
  ),
  190 => 
  array (
    'name' => 'Nice Care',
    'code' => NULL,
    'is_active' => true,
  ),
  191 => 
  array (
    'name' => 'Nipro',
    'code' => NULL,
    'is_active' => true,
  ),
  192 => 
  array (
    'name' => 'Norta',
    'code' => NULL,
    'is_active' => true,
  ),
  193 => 
  array (
    'name' => 'Novartis',
    'code' => NULL,
    'is_active' => true,
  ),
  194 => 
  array (
    'name' => 'Novell',
    'code' => NULL,
    'is_active' => true,
  ),
  195 => 
  array (
    'name' => 'Novo Nordisk',
    'code' => NULL,
    'is_active' => true,
  ),
  196 => 
  array (
    'name' => 'Oke Plast /Plesterin',
    'code' => NULL,
    'is_active' => true,
  ),
  197 => 
  array (
    'name' => 'Omniflow',
    'code' => NULL,
    'is_active' => true,
  ),
  198 => 
  array (
    'name' => 'One Med',
    'code' => NULL,
    'is_active' => true,
  ),
  199 => 
  array (
    'name' => 'OneMed',
    'code' => NULL,
    'is_active' => true,
  ),
  200 => 
  array (
    'name' => 'Onemed/Nice',
    'code' => NULL,
    'is_active' => true,
  ),
  201 => 
  array (
    'name' => 'On-X Medical',
    'code' => NULL,
    'is_active' => true,
  ),
  202 => 
  array (
    'name' => 'Organon',
    'code' => NULL,
    'is_active' => true,
  ),
  203 => 
  array (
    'name' => 'Otsuka',
    'code' => NULL,
    'is_active' => true,
  ),
  204 => 
  array (
    'name' => 'OTTO',
    'code' => NULL,
    'is_active' => true,
  ),
  205 => 
  array (
    'name' => 'Pahsco',
    'code' => NULL,
    'is_active' => true,
  ),
  206 => 
  array (
    'name' => 'Pakistan',
    'code' => NULL,
    'is_active' => true,
  ),
  207 => 
  array (
    'name' => 'PARKER',
    'code' => NULL,
    'is_active' => true,
  ),
  208 => 
  array (
    'name' => 'Pascho',
    'code' => NULL,
    'is_active' => true,
  ),
  209 => 
  array (
    'name' => 'Pfizer',
    'code' => NULL,
    'is_active' => true,
  ),
  210 => 
  array (
    'name' => 'PH',
    'code' => NULL,
    'is_active' => true,
  ),
  211 => 
  array (
    'name' => 'Phapros',
    'code' => NULL,
    'is_active' => true,
  ),
  212 => 
  array (
    'name' => 'Pharmacia',
    'code' => NULL,
    'is_active' => true,
  ),
  213 => 
  array (
    'name' => 'Pharmalab',
    'code' => NULL,
    'is_active' => true,
  ),
  214 => 
  array (
    'name' => 'Pharos',
    'code' => NULL,
    'is_active' => true,
  ),
  215 => 
  array (
    'name' => 'Ponco',
    'code' => NULL,
    'is_active' => true,
  ),
  216 => 
  array (
    'name' => 'portex',
    'code' => NULL,
    'is_active' => true,
  ),
  217 => 
  array (
    'name' => 'Prafa',
    'code' => NULL,
    'is_active' => true,
  ),
  218 => 
  array (
    'name' => 'Prima Dent',
    'code' => NULL,
    'is_active' => true,
  ),
  219 => 
  array (
    'name' => 'Prima Hexal',
    'code' => NULL,
    'is_active' => true,
  ),
  220 => 
  array (
    'name' => 'Pro Device',
    'code' => NULL,
    'is_active' => true,
  ),
  221 => 
  array (
    'name' => 'Project',
    'code' => NULL,
    'is_active' => true,
  ),
  222 => 
  array (
    'name' => 'Promed',
    'code' => NULL,
    'is_active' => true,
  ),
  223 => 
  array (
    'name' => 'PT. NUGRA KARSERA',
    'code' => NULL,
    'is_active' => true,
  ),
  224 => 
  array (
    'name' => 'PT. Perusahaan Perdagangan Ind',
    'code' => NULL,
    'is_active' => true,
  ),
  225 => 
  array (
    'name' => 'Pyridam',
    'code' => NULL,
    'is_active' => true,
  ),
  226 => 
  array (
    'name' => 'Quinton',
    'code' => NULL,
    'is_active' => true,
  ),
  227 => 
  array (
    'name' => 'Remedi',
    'code' => NULL,
    'is_active' => true,
  ),
  228 => 
  array (
    'name' => 'Remedi/Royal',
    'code' => NULL,
    'is_active' => true,
  ),
  229 => 
  array (
    'name' => 'Roche',
    'code' => NULL,
    'is_active' => true,
  ),
  230 => 
  array (
    'name' => 'Romson',
    'code' => NULL,
    'is_active' => true,
  ),
  231 => 
  array (
    'name' => 'Royal',
    'code' => NULL,
    'is_active' => true,
  ),
  232 => 
  array (
    'name' => 'RRC',
    'code' => NULL,
    'is_active' => true,
  ),
  233 => 
  array (
    'name' => 'Rusch',
    'code' => NULL,
    'is_active' => true,
  ),
  234 => 
  array (
    'name' => 'Rusch gold',
    'code' => NULL,
    'is_active' => true,
  ),
  235 => 
  array (
    'name' => 'S&N',
    'code' => NULL,
    'is_active' => true,
  ),
  236 => 
  array (
    'name' => 'S. Mortonn',
    'code' => NULL,
    'is_active' => true,
  ),
  237 => 
  array (
    'name' => 'S.Mortonn',
    'code' => NULL,
    'is_active' => true,
  ),
  238 => 
  array (
    'name' => 'Safeglove',
    'code' => NULL,
    'is_active' => true,
  ),
  239 => 
  array (
    'name' => 'Safety',
    'code' => NULL,
    'is_active' => true,
  ),
  240 => 
  array (
    'name' => 'Sahajanand',
    'code' => NULL,
    'is_active' => true,
  ),
  241 => 
  array (
    'name' => 'Salter',
    'code' => NULL,
    'is_active' => true,
  ),
  242 => 
  array (
    'name' => 'Sanbe Farma',
    'code' => NULL,
    'is_active' => true,
  ),
  243 => 
  array (
    'name' => 'Sanbe Vision',
    'code' => NULL,
    'is_active' => true,
  ),
  244 => 
  array (
    'name' => 'Sandoz',
    'code' => NULL,
    'is_active' => true,
  ),
  245 => 
  array (
    'name' => 'Sanofi Aventis',
    'code' => NULL,
    'is_active' => true,
  ),
  246 => 
  array (
    'name' => 'Schring ',
    'code' => NULL,
    'is_active' => true,
  ),
  247 => 
  array (
    'name' => 'Scimitar',
    'code' => NULL,
    'is_active' => true,
  ),
  248 => 
  array (
    'name' => 'SDM Lab',
    'code' => NULL,
    'is_active' => true,
  ),
  249 => 
  array (
    'name' => 'Semax Axomedica',
    'code' => NULL,
    'is_active' => true,
  ),
  250 => 
  array (
    'name' => 'Sensi Gloves',
    'code' => NULL,
    'is_active' => true,
  ),
  251 => 
  array (
    'name' => 'Septodont',
    'code' => NULL,
    'is_active' => true,
  ),
  252 => 
  array (
    'name' => 'Servier',
    'code' => NULL,
    'is_active' => true,
  ),
  253 => 
  array (
    'name' => 'Skifa',
    'code' => NULL,
    'is_active' => true,
  ),
  254 => 
  array (
    'name' => 'smi',
    'code' => NULL,
    'is_active' => true,
  ),
  255 => 
  array (
    'name' => 'SNA',
    'code' => NULL,
    'is_active' => true,
  ),
  256 => 
  array (
    'name' => 'Soho',
    'code' => NULL,
    'is_active' => true,
  ),
  257 => 
  array (
    'name' => 'SONY',
    'code' => NULL,
    'is_active' => true,
  ),
  258 => 
  array (
    'name' => 'Sphygmed',
    'code' => NULL,
    'is_active' => true,
  ),
  259 => 
  array (
    'name' => 'Squibb',
    'code' => NULL,
    'is_active' => true,
  ),
  260 => 
  array (
    'name' => 'St. Jude Medical USA',
    'code' => NULL,
    'is_active' => true,
  ),
  261 => 
  array (
    'name' => 'Stera',
    'code' => NULL,
    'is_active' => true,
  ),
  262 => 
  array (
    'name' => 'Sure Plus',
    'code' => NULL,
    'is_active' => true,
  ),
  263 => 
  array (
    'name' => 'Surflo',
    'code' => NULL,
    'is_active' => true,
  ),
  264 => 
  array (
    'name' => 'Surgiglove',
    'code' => NULL,
    'is_active' => true,
  ),
  265 => 
  array (
    'name' => 'Surgiroll',
    'code' => NULL,
    'is_active' => true,
  ),
  266 => 
  array (
    'name' => 'Surya Dermato Medica',
    'code' => NULL,
    'is_active' => true,
  ),
  267 => 
  array (
    'name' => 'Suture',
    'code' => NULL,
    'is_active' => true,
  ),
  268 => 
  array (
    'name' => 'Swan Morton',
    'code' => NULL,
    'is_active' => true,
  ),
  269 => 
  array (
    'name' => 'Syneture',
    'code' => NULL,
    'is_active' => true,
  ),
  270 => 
  array (
    'name' => 'Synthes',
    'code' => NULL,
    'is_active' => true,
  ),
  271 => 
  array (
    'name' => 'Taisho',
    'code' => NULL,
    'is_active' => true,
  ),
  272 => 
  array (
    'name' => 'Takeda',
    'code' => NULL,
    'is_active' => true,
  ),
  273 => 
  array (
    'name' => 'Talekris Biotherapeuitis',
    'code' => NULL,
    'is_active' => true,
  ),
  274 => 
  array (
    'name' => 'Tanabe',
    'code' => NULL,
    'is_active' => true,
  ),
  275 => 
  array (
    'name' => 'Terumo',
    'code' => NULL,
    'is_active' => true,
  ),
  276 => 
  array (
    'name' => 'Tirta Husada',
    'code' => NULL,
    'is_active' => true,
  ),
  277 => 
  array (
    'name' => 'Toyama',
    'code' => NULL,
    'is_active' => true,
  ),
  278 => 
  array (
    'name' => 'Tropica Mas P',
    'code' => NULL,
    'is_active' => true,
  ),
  279 => 
  array (
    'name' => 'Tunggal Idaman Abdi',
    'code' => NULL,
    'is_active' => true,
  ),
  280 => 
  array (
    'name' => 'UCB Pharma',
    'code' => NULL,
    'is_active' => true,
  ),
  281 => 
  array (
    'name' => 'Ultrasoniq',
    'code' => NULL,
    'is_active' => true,
  ),
  282 => 
  array (
    'name' => 'Unimed',
    'code' => NULL,
    'is_active' => true,
  ),
  283 => 
  array (
    'name' => 'Urocare',
    'code' => NULL,
    'is_active' => true,
  ),
  284 => 
  array (
    'name' => 'Uroplast',
    'code' => NULL,
    'is_active' => true,
  ),
  285 => 
  array (
    'name' => 'Ventisorb',
    'code' => NULL,
    'is_active' => true,
  ),
  286 => 
  array (
    'name' => 'Well Lead ',
    'code' => NULL,
    'is_active' => true,
  ),
  287 => 
  array (
    'name' => 'West Met',
    'code' => NULL,
    'is_active' => true,
  ),
  288 => 
  array (
    'name' => 'Widatra Bhakti',
    'code' => NULL,
    'is_active' => true,
  ),
  289 => 
  array (
    'name' => 'Win',
    'code' => NULL,
    'is_active' => true,
  ),
  290 => 
  array (
    'name' => 'Yupharin',
    'code' => NULL,
    'is_active' => true,
  ),
  291 => 
  array (
    'name' => 'Zambon',
    'code' => NULL,
    'is_active' => true,
  ),
  292 => 
  array (
    'name' => 'PT. Anugrah Argon Medica',
    'code' => NULL,
    'is_active' => false,
  ),
  293 => 
  array (
    'name' => 'Pilot',
    'code' => NULL,
    'is_active' => true,
  ),
  294 => 
  array (
    'name' => 'Snowman',
    'code' => NULL,
    'is_active' => true,
  ),
  295 => 
  array (
    'name' => 'Fiber Castle',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('manufacturers')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}