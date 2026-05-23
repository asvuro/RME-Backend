<?php

namespace Modules\GeneralActiveIngredient\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralActiveIngredientDatabaseSeeder extends Seeder
{
    /**
     * Seed data nyata dari dump master.referensi SIMGOS (jenis 42).
     */
    public function run(): void
    {
        $now = now();

        $rows = array (
  0 => 
  array (
    'name' => '-',
    'code' => NULL,
    'is_active' => true,
  ),
  1 => 
  array (
    'name' => 'acarbose',
    'code' => NULL,
    'is_active' => true,
  ),
  2 => 
  array (
    'name' => 'Acetazolamide',
    'code' => NULL,
    'is_active' => true,
  ),
  3 => 
  array (
    'name' => 'Acetylcysteine',
    'code' => NULL,
    'is_active' => true,
  ),
  4 => 
  array (
    'name' => 'Acetylsalicylic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  5 => 
  array (
    'name' => 'Aciclovir',
    'code' => NULL,
    'is_active' => true,
  ),
  6 => 
  array (
    'name' => 'Acyclovir',
    'code' => NULL,
    'is_active' => true,
  ),
  7 => 
  array (
    'name' => 'Adapalene',
    'code' => NULL,
    'is_active' => true,
  ),
  8 => 
  array (
    'name' => 'Adenosine triphosphate',
    'code' => NULL,
    'is_active' => true,
  ),
  9 => 
  array (
    'name' => 'Albendazole',
    'code' => NULL,
    'is_active' => true,
  ),
  10 => 
  array (
    'name' => 'Albumin',
    'code' => NULL,
    'is_active' => true,
  ),
  11 => 
  array (
    'name' => 'Alclometasone',
    'code' => NULL,
    'is_active' => true,
  ),
  12 => 
  array (
    'name' => 'Alendronate Na',
    'code' => NULL,
    'is_active' => true,
  ),
  13 => 
  array (
    'name' => 'Alendronic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  14 => 
  array (
    'name' => 'Alfacalcidol',
    'code' => NULL,
    'is_active' => true,
  ),
  15 => 
  array (
    'name' => 'Allopurinol',
    'code' => NULL,
    'is_active' => true,
  ),
  16 => 
  array (
    'name' => 'Allylestrenol',
    'code' => NULL,
    'is_active' => true,
  ),
  17 => 
  array (
    'name' => 'Alprazolam',
    'code' => NULL,
    'is_active' => true,
  ),
  18 => 
  array (
    'name' => 'Aluminium hydroxide',
    'code' => NULL,
    'is_active' => true,
  ),
  19 => 
  array (
    'name' => 'Aluminium hydroxide-magnesium carbonate',
    'code' => NULL,
    'is_active' => true,
  ),
  20 => 
  array (
    'name' => 'Aluminium hydroxide-magnesium hydroxide',
    'code' => NULL,
    'is_active' => true,
  ),
  21 => 
  array (
    'name' => 'Ambroxol',
    'code' => NULL,
    'is_active' => true,
  ),
  22 => 
  array (
    'name' => 'Amikacin',
    'code' => NULL,
    'is_active' => true,
  ),
  23 => 
  array (
    'name' => 'aminophyllin',
    'code' => NULL,
    'is_active' => true,
  ),
  24 => 
  array (
    'name' => 'Amiodarone',
    'code' => NULL,
    'is_active' => true,
  ),
  25 => 
  array (
    'name' => 'Amitryptilin',
    'code' => NULL,
    'is_active' => true,
  ),
  26 => 
  array (
    'name' => 'Amlodipine',
    'code' => NULL,
    'is_active' => true,
  ),
  27 => 
  array (
    'name' => 'Ammonium chloride',
    'code' => NULL,
    'is_active' => true,
  ),
  28 => 
  array (
    'name' => 'Amoxapine',
    'code' => NULL,
    'is_active' => true,
  ),
  29 => 
  array (
    'name' => 'Amoxicillin',
    'code' => NULL,
    'is_active' => true,
  ),
  30 => 
  array (
    'name' => 'Ampicillin',
    'code' => NULL,
    'is_active' => true,
  ),
  31 => 
  array (
    'name' => 'Ampicillin Sulbactam',
    'code' => NULL,
    'is_active' => true,
  ),
  32 => 
  array (
    'name' => 'Amylase',
    'code' => NULL,
    'is_active' => true,
  ),
  33 => 
  array (
    'name' => 'Anastrozole',
    'code' => NULL,
    'is_active' => true,
  ),
  34 => 
  array (
    'name' => 'Aripiprazole',
    'code' => NULL,
    'is_active' => true,
  ),
  35 => 
  array (
    'name' => 'Asam Amino',
    'code' => NULL,
    'is_active' => true,
  ),
  36 => 
  array (
    'name' => 'Asam Amino, Elektrolit',
    'code' => NULL,
    'is_active' => true,
  ),
  37 => 
  array (
    'name' => 'Asam amino,electrolit,dektrosa',
    'code' => NULL,
    'is_active' => true,
  ),
  38 => 
  array (
    'name' => 'Asam Asetilsalisilat',
    'code' => NULL,
    'is_active' => true,
  ),
  39 => 
  array (
    'name' => 'Asam Ibandronat',
    'code' => NULL,
    'is_active' => true,
  ),
  40 => 
  array (
    'name' => 'Asam Mefenamat',
    'code' => NULL,
    'is_active' => true,
  ),
  41 => 
  array (
    'name' => 'Asam Tranexamat',
    'code' => NULL,
    'is_active' => true,
  ),
  42 => 
  array (
    'name' => 'Ascorbic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  43 => 
  array (
    'name' => 'Asparaginase',
    'code' => NULL,
    'is_active' => true,
  ),
  44 => 
  array (
    'name' => 'Astaxanthin',
    'code' => NULL,
    'is_active' => true,
  ),
  45 => 
  array (
    'name' => 'Atenolol',
    'code' => NULL,
    'is_active' => true,
  ),
  46 => 
  array (
    'name' => 'Atorvastatin',
    'code' => NULL,
    'is_active' => true,
  ),
  47 => 
  array (
    'name' => 'Atracurium besilate',
    'code' => NULL,
    'is_active' => true,
  ),
  48 => 
  array (
    'name' => 'Atropin',
    'code' => NULL,
    'is_active' => true,
  ),
  49 => 
  array (
    'name' => 'Attapulgite',
    'code' => NULL,
    'is_active' => true,
  ),
  50 => 
  array (
    'name' => 'Azelaic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  51 => 
  array (
    'name' => 'Azithromycin',
    'code' => NULL,
    'is_active' => true,
  ),
  52 => 
  array (
    'name' => 'Aztreonam',
    'code' => NULL,
    'is_active' => true,
  ),
  53 => 
  array (
    'name' => 'Bacitracin',
    'code' => NULL,
    'is_active' => true,
  ),
  54 => 
  array (
    'name' => 'Baclofen',
    'code' => NULL,
    'is_active' => true,
  ),
  55 => 
  array (
    'name' => 'Belladonna',
    'code' => NULL,
    'is_active' => true,
  ),
  56 => 
  array (
    'name' => 'Benserazide',
    'code' => NULL,
    'is_active' => true,
  ),
  57 => 
  array (
    'name' => 'Bentonite',
    'code' => NULL,
    'is_active' => true,
  ),
  58 => 
  array (
    'name' => 'Beraprost sodium',
    'code' => NULL,
    'is_active' => true,
  ),
  59 => 
  array (
    'name' => 'Betacarotene',
    'code' => NULL,
    'is_active' => true,
  ),
  60 => 
  array (
    'name' => 'Betahistine',
    'code' => NULL,
    'is_active' => true,
  ),
  61 => 
  array (
    'name' => 'Betamethasone',
    'code' => NULL,
    'is_active' => true,
  ),
  62 => 
  array (
    'name' => 'bevacizumab',
    'code' => NULL,
    'is_active' => true,
  ),
  63 => 
  array (
    'name' => 'Bicalutamide',
    'code' => NULL,
    'is_active' => true,
  ),
  64 => 
  array (
    'name' => 'Bisacodyl',
    'code' => NULL,
    'is_active' => true,
  ),
  65 => 
  array (
    'name' => 'Bisoprolol',
    'code' => NULL,
    'is_active' => true,
  ),
  66 => 
  array (
    'name' => 'Bleomycin',
    'code' => NULL,
    'is_active' => true,
  ),
  67 => 
  array (
    'name' => 'Bromhexine',
    'code' => NULL,
    'is_active' => true,
  ),
  68 => 
  array (
    'name' => 'Bromocriptine',
    'code' => NULL,
    'is_active' => true,
  ),
  69 => 
  array (
    'name' => 'Brompheniramine',
    'code' => NULL,
    'is_active' => true,
  ),
  70 => 
  array (
    'name' => 'Budesonide',
    'code' => NULL,
    'is_active' => true,
  ),
  71 => 
  array (
    'name' => 'Bupivacaine',
    'code' => NULL,
    'is_active' => true,
  ),
  72 => 
  array (
    'name' => 'Caffeine',
    'code' => NULL,
    'is_active' => true,
  ),
  73 => 
  array (
    'name' => 'Calcipotriol',
    'code' => NULL,
    'is_active' => true,
  ),
  74 => 
  array (
    'name' => 'Calcitriol',
    'code' => NULL,
    'is_active' => true,
  ),
  75 => 
  array (
    'name' => 'Calcium',
    'code' => NULL,
    'is_active' => true,
  ),
  76 => 
  array (
    'name' => 'Calcium carbonate',
    'code' => NULL,
    'is_active' => true,
  ),
  77 => 
  array (
    'name' => 'Calcium folinat',
    'code' => NULL,
    'is_active' => true,
  ),
  78 => 
  array (
    'name' => 'Calcium gluconate',
    'code' => NULL,
    'is_active' => true,
  ),
  79 => 
  array (
    'name' => 'Calcium pantothenate',
    'code' => NULL,
    'is_active' => true,
  ),
  80 => 
  array (
    'name' => 'Candesartan cilexetil',
    'code' => NULL,
    'is_active' => true,
  ),
  81 => 
  array (
    'name' => 'Capecitabine',
    'code' => NULL,
    'is_active' => true,
  ),
  82 => 
  array (
    'name' => 'Captopril',
    'code' => NULL,
    'is_active' => true,
  ),
  83 => 
  array (
    'name' => 'Carbamazepine',
    'code' => NULL,
    'is_active' => true,
  ),
  84 => 
  array (
    'name' => 'Carbazochrome',
    'code' => NULL,
    'is_active' => true,
  ),
  85 => 
  array (
    'name' => 'Carbo Adsorben',
    'code' => NULL,
    'is_active' => true,
  ),
  86 => 
  array (
    'name' => 'Carboplatin',
    'code' => NULL,
    'is_active' => true,
  ),
  87 => 
  array (
    'name' => 'Carvedilol',
    'code' => NULL,
    'is_active' => true,
  ),
  88 => 
  array (
    'name' => 'Cefacior',
    'code' => NULL,
    'is_active' => true,
  ),
  89 => 
  array (
    'name' => 'Cefadroxil',
    'code' => NULL,
    'is_active' => true,
  ),
  90 => 
  array (
    'name' => 'Cefalexin',
    'code' => NULL,
    'is_active' => true,
  ),
  91 => 
  array (
    'name' => 'Cefazolin',
    'code' => NULL,
    'is_active' => true,
  ),
  92 => 
  array (
    'name' => 'Cefdinir',
    'code' => NULL,
    'is_active' => true,
  ),
  93 => 
  array (
    'name' => 'Cefditoren',
    'code' => NULL,
    'is_active' => true,
  ),
  94 => 
  array (
    'name' => 'Cefepime',
    'code' => NULL,
    'is_active' => true,
  ),
  95 => 
  array (
    'name' => 'Cefixime',
    'code' => NULL,
    'is_active' => true,
  ),
  96 => 
  array (
    'name' => 'Cefmetazole = Cefmetazon',
    'code' => NULL,
    'is_active' => true,
  ),
  97 => 
  array (
    'name' => 'Cefoperazone',
    'code' => NULL,
    'is_active' => true,
  ),
  98 => 
  array (
    'name' => 'Cefotaxime',
    'code' => NULL,
    'is_active' => true,
  ),
  99 => 
  array (
    'name' => 'Cefotiam',
    'code' => NULL,
    'is_active' => true,
  ),
  100 => 
  array (
    'name' => 'Cefpirome',
    'code' => NULL,
    'is_active' => true,
  ),
  101 => 
  array (
    'name' => 'Cefprozil',
    'code' => NULL,
    'is_active' => true,
  ),
  102 => 
  array (
    'name' => 'Ceftazidime',
    'code' => NULL,
    'is_active' => true,
  ),
  103 => 
  array (
    'name' => 'Ceftizoxime',
    'code' => NULL,
    'is_active' => true,
  ),
  104 => 
  array (
    'name' => 'Ceftriaxone',
    'code' => NULL,
    'is_active' => true,
  ),
  105 => 
  array (
    'name' => 'Cefuroxime',
    'code' => NULL,
    'is_active' => true,
  ),
  106 => 
  array (
    'name' => 'Celecoxib',
    'code' => NULL,
    'is_active' => true,
  ),
  107 => 
  array (
    'name' => 'Cetirizine',
    'code' => NULL,
    'is_active' => true,
  ),
  108 => 
  array (
    'name' => 'Chenodeoxycholic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  109 => 
  array (
    'name' => 'Chloramphenicol',
    'code' => NULL,
    'is_active' => true,
  ),
  110 => 
  array (
    'name' => 'Chlordiazepoxide',
    'code' => NULL,
    'is_active' => true,
  ),
  111 => 
  array (
    'name' => 'Chlorhexidine',
    'code' => NULL,
    'is_active' => true,
  ),
  112 => 
  array (
    'name' => 'Chlorpheniramine',
    'code' => NULL,
    'is_active' => true,
  ),
  113 => 
  array (
    'name' => 'Chlorpromazine',
    'code' => NULL,
    'is_active' => true,
  ),
  114 => 
  array (
    'name' => 'Cholestyramine',
    'code' => NULL,
    'is_active' => true,
  ),
  115 => 
  array (
    'name' => 'Choline bitartrate',
    'code' => NULL,
    'is_active' => true,
  ),
  116 => 
  array (
    'name' => 'Chondroitin',
    'code' => NULL,
    'is_active' => true,
  ),
  117 => 
  array (
    'name' => 'Cilastatin',
    'code' => NULL,
    'is_active' => true,
  ),
  118 => 
  array (
    'name' => 'Cilostazol',
    'code' => NULL,
    'is_active' => true,
  ),
  119 => 
  array (
    'name' => 'Cimetidine',
    'code' => NULL,
    'is_active' => true,
  ),
  120 => 
  array (
    'name' => 'Cinnarizine',
    'code' => NULL,
    'is_active' => true,
  ),
  121 => 
  array (
    'name' => 'Ciprofloxacin',
    'code' => NULL,
    'is_active' => true,
  ),
  122 => 
  array (
    'name' => 'Cisapride',
    'code' => NULL,
    'is_active' => true,
  ),
  123 => 
  array (
    'name' => 'Cisplatin',
    'code' => NULL,
    'is_active' => true,
  ),
  124 => 
  array (
    'name' => 'Citalopram',
    'code' => NULL,
    'is_active' => true,
  ),
  125 => 
  array (
    'name' => 'Citarabin',
    'code' => NULL,
    'is_active' => true,
  ),
  126 => 
  array (
    'name' => 'Citicoline',
    'code' => NULL,
    'is_active' => true,
  ),
  127 => 
  array (
    'name' => 'Clarithromycin',
    'code' => NULL,
    'is_active' => true,
  ),
  128 => 
  array (
    'name' => 'Clavulanate K',
    'code' => NULL,
    'is_active' => true,
  ),
  129 => 
  array (
    'name' => 'Clavulanic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  130 => 
  array (
    'name' => 'Clebopride',
    'code' => NULL,
    'is_active' => true,
  ),
  131 => 
  array (
    'name' => 'Clenbuterol',
    'code' => NULL,
    'is_active' => true,
  ),
  132 => 
  array (
    'name' => 'Clidinium bromide',
    'code' => NULL,
    'is_active' => true,
  ),
  133 => 
  array (
    'name' => 'Clindamycin',
    'code' => NULL,
    'is_active' => true,
  ),
  134 => 
  array (
    'name' => 'Clobazam',
    'code' => NULL,
    'is_active' => true,
  ),
  135 => 
  array (
    'name' => 'Clobetasol',
    'code' => NULL,
    'is_active' => true,
  ),
  136 => 
  array (
    'name' => 'Clobutinol',
    'code' => NULL,
    'is_active' => true,
  ),
  137 => 
  array (
    'name' => 'Clodronate Natrium',
    'code' => NULL,
    'is_active' => true,
  ),
  138 => 
  array (
    'name' => 'Clomifene',
    'code' => NULL,
    'is_active' => true,
  ),
  139 => 
  array (
    'name' => 'Clomipramine',
    'code' => NULL,
    'is_active' => true,
  ),
  140 => 
  array (
    'name' => 'Clonidine',
    'code' => NULL,
    'is_active' => true,
  ),
  141 => 
  array (
    'name' => 'Clopidogrel',
    'code' => NULL,
    'is_active' => true,
  ),
  142 => 
  array (
    'name' => 'Clorhesadine Gluconate',
    'code' => NULL,
    'is_active' => true,
  ),
  143 => 
  array (
    'name' => 'Clozapine',
    'code' => NULL,
    'is_active' => true,
  ),
  144 => 
  array (
    'name' => 'Co-dergocrine mesylate',
    'code' => NULL,
    'is_active' => true,
  ),
  145 => 
  array (
    'name' => 'Co-trimoxazole',
    'code' => NULL,
    'is_active' => true,
  ),
  146 => 
  array (
    'name' => 'Cod Liver Oil',
    'code' => NULL,
    'is_active' => true,
  ),
  147 => 
  array (
    'name' => 'Codeine',
    'code' => NULL,
    'is_active' => true,
  ),
  148 => 
  array (
    'name' => 'Coenzyme Q10',
    'code' => NULL,
    'is_active' => true,
  ),
  149 => 
  array (
    'name' => 'Colchicine',
    'code' => NULL,
    'is_active' => true,
  ),
  150 => 
  array (
    'name' => 'Colecalciferol',
    'code' => NULL,
    'is_active' => true,
  ),
  151 => 
  array (
    'name' => 'Colistin sulphate = Polymyxin E sulphate',
    'code' => NULL,
    'is_active' => true,
  ),
  152 => 
  array (
    'name' => 'Curcuminoid',
    'code' => NULL,
    'is_active' => true,
  ),
  153 => 
  array (
    'name' => 'Cyanocobalamin = Vit B12',
    'code' => NULL,
    'is_active' => true,
  ),
  154 => 
  array (
    'name' => 'Cyclosporin',
    'code' => NULL,
    'is_active' => true,
  ),
  155 => 
  array (
    'name' => 'Dacarbazin',
    'code' => NULL,
    'is_active' => true,
  ),
  156 => 
  array (
    'name' => 'Danazol',
    'code' => NULL,
    'is_active' => true,
  ),
  157 => 
  array (
    'name' => 'Daunorubicin',
    'code' => NULL,
    'is_active' => true,
  ),
  158 => 
  array (
    'name' => 'Deferasirox',
    'code' => NULL,
    'is_active' => true,
  ),
  159 => 
  array (
    'name' => 'Deferoksamin',
    'code' => NULL,
    'is_active' => true,
  ),
  160 => 
  array (
    'name' => 'Desferrioxamine',
    'code' => NULL,
    'is_active' => true,
  ),
  161 => 
  array (
    'name' => 'Desloratadine',
    'code' => NULL,
    'is_active' => true,
  ),
  162 => 
  array (
    'name' => 'Desonide',
    'code' => NULL,
    'is_active' => true,
  ),
  163 => 
  array (
    'name' => 'Desoximethasone',
    'code' => NULL,
    'is_active' => true,
  ),
  164 => 
  array (
    'name' => 'Dexamethasone',
    'code' => NULL,
    'is_active' => true,
  ),
  165 => 
  array (
    'name' => 'Dexchlorpheniramine',
    'code' => NULL,
    'is_active' => true,
  ),
  166 => 
  array (
    'name' => 'Dexketoprofen trometamol',
    'code' => NULL,
    'is_active' => true,
  ),
  167 => 
  array (
    'name' => 'Dextromethorphan',
    'code' => NULL,
    'is_active' => true,
  ),
  168 => 
  array (
    'name' => 'DHA',
    'code' => NULL,
    'is_active' => true,
  ),
  169 => 
  array (
    'name' => 'Diacerein',
    'code' => NULL,
    'is_active' => true,
  ),
  170 => 
  array (
    'name' => 'Diazepam',
    'code' => NULL,
    'is_active' => true,
  ),
  171 => 
  array (
    'name' => 'Dibekacin',
    'code' => NULL,
    'is_active' => true,
  ),
  172 => 
  array (
    'name' => 'Diclofenac',
    'code' => NULL,
    'is_active' => true,
  ),
  173 => 
  array (
    'name' => 'Diclofenac diethylammonium',
    'code' => NULL,
    'is_active' => true,
  ),
  174 => 
  array (
    'name' => 'Diflucortolone',
    'code' => NULL,
    'is_active' => true,
  ),
  175 => 
  array (
    'name' => 'Digoxin',
    'code' => NULL,
    'is_active' => true,
  ),
  176 => 
  array (
    'name' => 'Diltiazem',
    'code' => NULL,
    'is_active' => true,
  ),
  177 => 
  array (
    'name' => 'Dimenhydrinate',
    'code' => NULL,
    'is_active' => true,
  ),
  178 => 
  array (
    'name' => 'Dioctyl sodium sulphosuccinate = Docusate sodium',
    'code' => NULL,
    'is_active' => true,
  ),
  179 => 
  array (
    'name' => 'Diosmin',
    'code' => NULL,
    'is_active' => true,
  ),
  180 => 
  array (
    'name' => 'Diphenhydramine',
    'code' => NULL,
    'is_active' => true,
  ),
  181 => 
  array (
    'name' => 'Diphenylhydantoin',
    'code' => NULL,
    'is_active' => true,
  ),
  182 => 
  array (
    'name' => 'Dipyridamole',
    'code' => NULL,
    'is_active' => true,
  ),
  183 => 
  array (
    'name' => 'Divalproex Na',
    'code' => NULL,
    'is_active' => true,
  ),
  184 => 
  array (
    'name' => 'Dobutamine',
    'code' => NULL,
    'is_active' => true,
  ),
  185 => 
  array (
    'name' => 'docetaxel',
    'code' => NULL,
    'is_active' => true,
  ),
  186 => 
  array (
    'name' => 'Domperidone',
    'code' => NULL,
    'is_active' => true,
  ),
  187 => 
  array (
    'name' => 'Donepezil',
    'code' => NULL,
    'is_active' => true,
  ),
  188 => 
  array (
    'name' => 'Dopamine',
    'code' => NULL,
    'is_active' => true,
  ),
  189 => 
  array (
    'name' => 'Doripenem',
    'code' => NULL,
    'is_active' => true,
  ),
  190 => 
  array (
    'name' => 'Doxorubicin',
    'code' => NULL,
    'is_active' => true,
  ),
  191 => 
  array (
    'name' => 'Doxycycline',
    'code' => NULL,
    'is_active' => true,
  ),
  192 => 
  array (
    'name' => 'Duloxetine',
    'code' => NULL,
    'is_active' => true,
  ),
  193 => 
  array (
    'name' => 'Dydrogesterone',
    'code' => NULL,
    'is_active' => true,
  ),
  194 => 
  array (
    'name' => 'Echinacea dry extr',
    'code' => NULL,
    'is_active' => true,
  ),
  195 => 
  array (
    'name' => 'Elderberry Extract',
    'code' => NULL,
    'is_active' => true,
  ),
  196 => 
  array (
    'name' => 'Enalapril',
    'code' => NULL,
    'is_active' => true,
  ),
  197 => 
  array (
    'name' => 'Enflurane',
    'code' => NULL,
    'is_active' => true,
  ),
  198 => 
  array (
    'name' => 'Enoxaparin',
    'code' => NULL,
    'is_active' => true,
  ),
  199 => 
  array (
    'name' => 'Eperisone',
    'code' => NULL,
    'is_active' => true,
  ),
  200 => 
  array (
    'name' => 'Ephedrine',
    'code' => NULL,
    'is_active' => true,
  ),
  201 => 
  array (
    'name' => 'Epirubicin',
    'code' => NULL,
    'is_active' => true,
  ),
  202 => 
  array (
    'name' => 'Epoetin alfa = Recombinant human erythropoietin',
    'code' => NULL,
    'is_active' => true,
  ),
  203 => 
  array (
    'name' => 'Erdosteine',
    'code' => NULL,
    'is_active' => true,
  ),
  204 => 
  array (
    'name' => 'Ergotamine',
    'code' => NULL,
    'is_active' => true,
  ),
  205 => 
  array (
    'name' => 'Erythromycin',
    'code' => NULL,
    'is_active' => true,
  ),
  206 => 
  array (
    'name' => 'Esomeprazole',
    'code' => NULL,
    'is_active' => true,
  ),
  207 => 
  array (
    'name' => 'Estazolam',
    'code' => NULL,
    'is_active' => true,
  ),
  208 => 
  array (
    'name' => 'Ethambutol',
    'code' => NULL,
    'is_active' => true,
  ),
  209 => 
  array (
    'name' => 'Ethamsylate',
    'code' => NULL,
    'is_active' => true,
  ),
  210 => 
  array (
    'name' => 'Ethinyl estradiol',
    'code' => NULL,
    'is_active' => true,
  ),
  211 => 
  array (
    'name' => 'Ethylhexyl methoxycinnamate',
    'code' => NULL,
    'is_active' => true,
  ),
  212 => 
  array (
    'name' => 'Etodolac',
    'code' => NULL,
    'is_active' => true,
  ),
  213 => 
  array (
    'name' => 'Etoposid',
    'code' => NULL,
    'is_active' => true,
  ),
  214 => 
  array (
    'name' => 'Ezetimibe',
    'code' => NULL,
    'is_active' => true,
  ),
  215 => 
  array (
    'name' => 'Factor IX',
    'code' => NULL,
    'is_active' => true,
  ),
  216 => 
  array (
    'name' => 'Factor VII',
    'code' => NULL,
    'is_active' => true,
  ),
  217 => 
  array (
    'name' => 'Factor VIII',
    'code' => NULL,
    'is_active' => true,
  ),
  218 => 
  array (
    'name' => 'Famotidine',
    'code' => NULL,
    'is_active' => true,
  ),
  219 => 
  array (
    'name' => 'Felodipine',
    'code' => NULL,
    'is_active' => true,
  ),
  220 => 
  array (
    'name' => 'Fenbufen',
    'code' => NULL,
    'is_active' => true,
  ),
  221 => 
  array (
    'name' => 'Fenofibrate',
    'code' => NULL,
    'is_active' => true,
  ),
  222 => 
  array (
    'name' => 'Fenoterol',
    'code' => NULL,
    'is_active' => true,
  ),
  223 => 
  array (
    'name' => 'Fentanyl',
    'code' => NULL,
    'is_active' => true,
  ),
  224 => 
  array (
    'name' => 'Fexofenadine',
    'code' => NULL,
    'is_active' => true,
  ),
  225 => 
  array (
    'name' => 'Filgrastim',
    'code' => NULL,
    'is_active' => true,
  ),
  226 => 
  array (
    'name' => 'Finasteride',
    'code' => NULL,
    'is_active' => true,
  ),
  227 => 
  array (
    'name' => 'Fluconazole',
    'code' => NULL,
    'is_active' => true,
  ),
  228 => 
  array (
    'name' => 'Flumazenil',
    'code' => NULL,
    'is_active' => true,
  ),
  229 => 
  array (
    'name' => 'Flunarizine',
    'code' => NULL,
    'is_active' => true,
  ),
  230 => 
  array (
    'name' => 'Fluocinolone',
    'code' => NULL,
    'is_active' => true,
  ),
  231 => 
  array (
    'name' => 'Fluocortolone',
    'code' => NULL,
    'is_active' => true,
  ),
  232 => 
  array (
    'name' => 'Fluorouracil',
    'code' => NULL,
    'is_active' => true,
  ),
  233 => 
  array (
    'name' => 'Fluoxetine',
    'code' => NULL,
    'is_active' => true,
  ),
  234 => 
  array (
    'name' => 'Fluphenazine',
    'code' => NULL,
    'is_active' => true,
  ),
  235 => 
  array (
    'name' => 'Fluticasone',
    'code' => NULL,
    'is_active' => true,
  ),
  236 => 
  array (
    'name' => 'Fluvastatin',
    'code' => NULL,
    'is_active' => true,
  ),
  237 => 
  array (
    'name' => 'Fluvoxamine',
    'code' => NULL,
    'is_active' => true,
  ),
  238 => 
  array (
    'name' => 'Folic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  239 => 
  array (
    'name' => 'Fondaparinux sodium',
    'code' => NULL,
    'is_active' => true,
  ),
  240 => 
  array (
    'name' => 'Formoterol',
    'code' => NULL,
    'is_active' => true,
  ),
  241 => 
  array (
    'name' => 'Fosfomicin',
    'code' => NULL,
    'is_active' => true,
  ),
  242 => 
  array (
    'name' => 'Fosinopril',
    'code' => NULL,
    'is_active' => true,
  ),
  243 => 
  array (
    'name' => 'Framycetin',
    'code' => NULL,
    'is_active' => true,
  ),
  244 => 
  array (
    'name' => 'Fucoidan',
    'code' => NULL,
    'is_active' => true,
  ),
  245 => 
  array (
    'name' => 'Furosemide',
    'code' => NULL,
    'is_active' => true,
  ),
  246 => 
  array (
    'name' => 'Fursultiamine',
    'code' => NULL,
    'is_active' => true,
  ),
  247 => 
  array (
    'name' => 'Fusidic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  248 => 
  array (
    'name' => 'Gabapentin',
    'code' => NULL,
    'is_active' => true,
  ),
  249 => 
  array (
    'name' => 'Gatifloxacin',
    'code' => NULL,
    'is_active' => true,
  ),
  250 => 
  array (
    'name' => 'gefitinib',
    'code' => NULL,
    'is_active' => true,
  ),
  251 => 
  array (
    'name' => 'Gemcitabine',
    'code' => NULL,
    'is_active' => true,
  ),
  252 => 
  array (
    'name' => 'Gemfibrozil',
    'code' => NULL,
    'is_active' => true,
  ),
  253 => 
  array (
    'name' => 'Gentamicin',
    'code' => NULL,
    'is_active' => true,
  ),
  254 => 
  array (
    'name' => 'Ginkgo biloba',
    'code' => NULL,
    'is_active' => true,
  ),
  255 => 
  array (
    'name' => 'Glibenclamide',
    'code' => NULL,
    'is_active' => true,
  ),
  256 => 
  array (
    'name' => 'Gliclazide',
    'code' => NULL,
    'is_active' => true,
  ),
  257 => 
  array (
    'name' => 'Glimepiride',
    'code' => NULL,
    'is_active' => true,
  ),
  258 => 
  array (
    'name' => 'gliseril Guaiakolat',
    'code' => NULL,
    'is_active' => true,
  ),
  259 => 
  array (
    'name' => 'Glucosamine',
    'code' => NULL,
    'is_active' => true,
  ),
  260 => 
  array (
    'name' => 'Glycerin = Glycerol',
    'code' => NULL,
    'is_active' => true,
  ),
  261 => 
  array (
    'name' => 'Glyceryl guaiacolate = Guaifenesin',
    'code' => NULL,
    'is_active' => true,
  ),
  262 => 
  array (
    'name' => 'Goserelin',
    'code' => NULL,
    'is_active' => true,
  ),
  263 => 
  array (
    'name' => 'Goserelin Asetat',
    'code' => NULL,
    'is_active' => true,
  ),
  264 => 
  array (
    'name' => 'Granisetron',
    'code' => NULL,
    'is_active' => true,
  ),
  265 => 
  array (
    'name' => 'Griseofulvin',
    'code' => NULL,
    'is_active' => true,
  ),
  266 => 
  array (
    'name' => 'Guaifenesin',
    'code' => NULL,
    'is_active' => true,
  ),
  267 => 
  array (
    'name' => 'Haloperidol',
    'code' => NULL,
    'is_active' => true,
  ),
  268 => 
  array (
    'name' => 'Halothane',
    'code' => NULL,
    'is_active' => true,
  ),
  269 => 
  array (
    'name' => 'Heparin',
    'code' => NULL,
    'is_active' => true,
  ),
  270 => 
  array (
    'name' => 'Hesperidin',
    'code' => NULL,
    'is_active' => true,
  ),
  271 => 
  array (
    'name' => 'hidroklortiazide',
    'code' => NULL,
    'is_active' => true,
  ),
  272 => 
  array (
    'name' => 'Hidrokortison',
    'code' => NULL,
    'is_active' => true,
  ),
  273 => 
  array (
    'name' => 'Hidrosmin',
    'code' => NULL,
    'is_active' => true,
  ),
  274 => 
  array (
    'name' => 'Hidroxyethyl starch',
    'code' => NULL,
    'is_active' => true,
  ),
  275 => 
  array (
    'name' => 'Hyaluronic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  276 => 
  array (
    'name' => 'Hydrochlorothiazide',
    'code' => NULL,
    'is_active' => true,
  ),
  277 => 
  array (
    'name' => 'Hydromorphone HCl',
    'code' => NULL,
    'is_active' => true,
  ),
  278 => 
  array (
    'name' => 'Hydromorphone hydrochloride',
    'code' => NULL,
    'is_active' => true,
  ),
  279 => 
  array (
    'name' => 'Hydroquinone',
    'code' => NULL,
    'is_active' => true,
  ),
  280 => 
  array (
    'name' => 'Hydroxil Urea',
    'code' => NULL,
    'is_active' => true,
  ),
  281 => 
  array (
    'name' => 'Hydroxycarbamide = Hydroxyurea',
    'code' => NULL,
    'is_active' => true,
  ),
  282 => 
  array (
    'name' => 'Hydroxyzine',
    'code' => NULL,
    'is_active' => true,
  ),
  283 => 
  array (
    'name' => 'Hyoscine',
    'code' => NULL,
    'is_active' => true,
  ),
  284 => 
  array (
    'name' => 'Ibuprofen',
    'code' => NULL,
    'is_active' => true,
  ),
  285 => 
  array (
    'name' => 'Ifosfamid',
    'code' => NULL,
    'is_active' => true,
  ),
  286 => 
  array (
    'name' => 'Imatinib',
    'code' => NULL,
    'is_active' => true,
  ),
  287 => 
  array (
    'name' => 'Imidapril',
    'code' => NULL,
    'is_active' => true,
  ),
  288 => 
  array (
    'name' => 'Imipenem',
    'code' => NULL,
    'is_active' => true,
  ),
  289 => 
  array (
    'name' => 'imipramin',
    'code' => NULL,
    'is_active' => true,
  ),
  290 => 
  array (
    'name' => 'Indapamide',
    'code' => NULL,
    'is_active' => true,
  ),
  291 => 
  array (
    'name' => 'Indometacin',
    'code' => NULL,
    'is_active' => true,
  ),
  292 => 
  array (
    'name' => 'Insulin aspart',
    'code' => NULL,
    'is_active' => true,
  ),
  293 => 
  array (
    'name' => 'Insulin glargine',
    'code' => NULL,
    'is_active' => true,
  ),
  294 => 
  array (
    'name' => 'Iodixanol',
    'code' => NULL,
    'is_active' => true,
  ),
  295 => 
  array (
    'name' => 'Iohexol',
    'code' => NULL,
    'is_active' => true,
  ),
  296 => 
  array (
    'name' => 'Ipratropium bromide',
    'code' => NULL,
    'is_active' => true,
  ),
  297 => 
  array (
    'name' => 'Irbesartan',
    'code' => NULL,
    'is_active' => true,
  ),
  298 => 
  array (
    'name' => 'Irinotecan HCl',
    'code' => NULL,
    'is_active' => true,
  ),
  299 => 
  array (
    'name' => 'Iron dextran',
    'code' => NULL,
    'is_active' => true,
  ),
  300 => 
  array (
    'name' => 'Isoflurane',
    'code' => NULL,
    'is_active' => true,
  ),
  301 => 
  array (
    'name' => 'isoniazid',
    'code' => NULL,
    'is_active' => true,
  ),
  302 => 
  array (
    'name' => 'Isosorbide dinitrate',
    'code' => NULL,
    'is_active' => true,
  ),
  303 => 
  array (
    'name' => 'Isosorbide mononitrate = Isosorbide -5- mononitrad',
    'code' => NULL,
    'is_active' => true,
  ),
  304 => 
  array (
    'name' => 'Isoxsuprine',
    'code' => NULL,
    'is_active' => true,
  ),
  305 => 
  array (
    'name' => 'Ispaghula = Psyllium',
    'code' => NULL,
    'is_active' => true,
  ),
  306 => 
  array (
    'name' => 'Itraconazole',
    'code' => NULL,
    'is_active' => true,
  ),
  307 => 
  array (
    'name' => 'Kalium Diklofenak',
    'code' => NULL,
    'is_active' => true,
  ),
  308 => 
  array (
    'name' => 'Kalium Klorida',
    'code' => NULL,
    'is_active' => true,
  ),
  309 => 
  array (
    'name' => 'Kanamycin',
    'code' => NULL,
    'is_active' => true,
  ),
  310 => 
  array (
    'name' => 'Kaolin',
    'code' => NULL,
    'is_active' => true,
  ),
  311 => 
  array (
    'name' => 'Ketamine',
    'code' => NULL,
    'is_active' => true,
  ),
  312 => 
  array (
    'name' => 'Ketoconazole',
    'code' => NULL,
    'is_active' => true,
  ),
  313 => 
  array (
    'name' => 'Ketoprofen',
    'code' => NULL,
    'is_active' => true,
  ),
  314 => 
  array (
    'name' => 'Ketorolac tromethamine = Ketorolac trometamol',
    'code' => NULL,
    'is_active' => true,
  ),
  315 => 
  array (
    'name' => 'klozapine',
    'code' => NULL,
    'is_active' => true,
  ),
  316 => 
  array (
    'name' => 'Lactobacillus',
    'code' => NULL,
    'is_active' => true,
  ),
  317 => 
  array (
    'name' => 'Lactulose',
    'code' => NULL,
    'is_active' => true,
  ),
  318 => 
  array (
    'name' => 'Lansoprazole',
    'code' => NULL,
    'is_active' => true,
  ),
  319 => 
  array (
    'name' => 'Lecithin',
    'code' => NULL,
    'is_active' => true,
  ),
  320 => 
  array (
    'name' => 'Lercanidipine',
    'code' => NULL,
    'is_active' => true,
  ),
  321 => 
  array (
    'name' => 'Letrozol',
    'code' => NULL,
    'is_active' => true,
  ),
  322 => 
  array (
    'name' => 'Leucovorin calcium = Calcium folinate',
    'code' => NULL,
    'is_active' => true,
  ),
  323 => 
  array (
    'name' => 'Leuprorelin',
    'code' => NULL,
    'is_active' => true,
  ),
  324 => 
  array (
    'name' => 'Levobupivacaine',
    'code' => NULL,
    'is_active' => true,
  ),
  325 => 
  array (
    'name' => 'Levodopa',
    'code' => NULL,
    'is_active' => true,
  ),
  326 => 
  array (
    'name' => 'Levofloxacin',
    'code' => NULL,
    'is_active' => true,
  ),
  327 => 
  array (
    'name' => 'Levonorgestrel',
    'code' => NULL,
    'is_active' => true,
  ),
  328 => 
  array (
    'name' => 'Levothyroxine sodium',
    'code' => NULL,
    'is_active' => true,
  ),
  329 => 
  array (
    'name' => 'Lidocaine',
    'code' => NULL,
    'is_active' => true,
  ),
  330 => 
  array (
    'name' => 'Lincomycin',
    'code' => NULL,
    'is_active' => true,
  ),
  331 => 
  array (
    'name' => 'Lipoic acid = Thioctic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  332 => 
  array (
    'name' => 'Liquiritiae radix',
    'code' => NULL,
    'is_active' => true,
  ),
  333 => 
  array (
    'name' => 'Lisinopril',
    'code' => NULL,
    'is_active' => true,
  ),
  334 => 
  array (
    'name' => 'Lithium',
    'code' => NULL,
    'is_active' => true,
  ),
  335 => 
  array (
    'name' => 'Loperamide',
    'code' => NULL,
    'is_active' => true,
  ),
  336 => 
  array (
    'name' => 'Loratadine',
    'code' => NULL,
    'is_active' => true,
  ),
  337 => 
  array (
    'name' => 'Lorazepam',
    'code' => NULL,
    'is_active' => true,
  ),
  338 => 
  array (
    'name' => 'Losartan',
    'code' => NULL,
    'is_active' => true,
  ),
  339 => 
  array (
    'name' => 'Loxoprofen',
    'code' => NULL,
    'is_active' => true,
  ),
  340 => 
  array (
    'name' => 'Lutein',
    'code' => NULL,
    'is_active' => true,
  ),
  341 => 
  array (
    'name' => 'Lycopene',
    'code' => NULL,
    'is_active' => true,
  ),
  342 => 
  array (
    'name' => 'Lynestrenol',
    'code' => NULL,
    'is_active' => true,
  ),
  343 => 
  array (
    'name' => 'Magaldrate',
    'code' => NULL,
    'is_active' => true,
  ),
  344 => 
  array (
    'name' => 'Magnesium aspartate',
    'code' => NULL,
    'is_active' => true,
  ),
  345 => 
  array (
    'name' => 'Magnesium hydroxide',
    'code' => NULL,
    'is_active' => true,
  ),
  346 => 
  array (
    'name' => 'Magnesium trisilicate',
    'code' => NULL,
    'is_active' => true,
  ),
  347 => 
  array (
    'name' => 'Mannitol',
    'code' => NULL,
    'is_active' => true,
  ),
  348 => 
  array (
    'name' => 'Maprotilin',
    'code' => NULL,
    'is_active' => true,
  ),
  349 => 
  array (
    'name' => 'Maprotiline',
    'code' => NULL,
    'is_active' => true,
  ),
  350 => 
  array (
    'name' => 'mebend',
    'code' => NULL,
    'is_active' => true,
  ),
  351 => 
  array (
    'name' => 'Mebendazol',
    'code' => NULL,
    'is_active' => true,
  ),
  352 => 
  array (
    'name' => 'Mebhydrolin',
    'code' => NULL,
    'is_active' => true,
  ),
  353 => 
  array (
    'name' => 'mecobalamin',
    'code' => NULL,
    'is_active' => true,
  ),
  354 => 
  array (
    'name' => 'Mefenamic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  355 => 
  array (
    'name' => 'Meloxicam',
    'code' => NULL,
    'is_active' => true,
  ),
  356 => 
  array (
    'name' => 'Menadione',
    'code' => NULL,
    'is_active' => true,
  ),
  357 => 
  array (
    'name' => 'Menthol',
    'code' => NULL,
    'is_active' => true,
  ),
  358 => 
  array (
    'name' => 'Mepivacaine HCl',
    'code' => NULL,
    'is_active' => true,
  ),
  359 => 
  array (
    'name' => 'Mercaptopurin',
    'code' => NULL,
    'is_active' => true,
  ),
  360 => 
  array (
    'name' => 'Meropenem',
    'code' => NULL,
    'is_active' => true,
  ),
  361 => 
  array (
    'name' => 'Mesalazine',
    'code' => NULL,
    'is_active' => true,
  ),
  362 => 
  array (
    'name' => 'Metamizole sodium = Methampyrone',
    'code' => NULL,
    'is_active' => true,
  ),
  363 => 
  array (
    'name' => 'Metformin',
    'code' => NULL,
    'is_active' => true,
  ),
  364 => 
  array (
    'name' => 'methad',
    'code' => NULL,
    'is_active' => true,
  ),
  365 => 
  array (
    'name' => 'Methadone',
    'code' => NULL,
    'is_active' => true,
  ),
  366 => 
  array (
    'name' => 'Methisoprinol = Inosine dimepranol acedoben',
    'code' => NULL,
    'is_active' => true,
  ),
  367 => 
  array (
    'name' => 'Methyldopa',
    'code' => NULL,
    'is_active' => true,
  ),
  368 => 
  array (
    'name' => 'Methylergometrine',
    'code' => NULL,
    'is_active' => true,
  ),
  369 => 
  array (
    'name' => 'Methylphenidate',
    'code' => NULL,
    'is_active' => true,
  ),
  370 => 
  array (
    'name' => 'Methylprednisolon',
    'code' => NULL,
    'is_active' => true,
  ),
  371 => 
  array (
    'name' => 'Metoclopramide hydrocloride',
    'code' => NULL,
    'is_active' => true,
  ),
  372 => 
  array (
    'name' => 'Metotreksat',
    'code' => NULL,
    'is_active' => true,
  ),
  373 => 
  array (
    'name' => 'Metronidazole',
    'code' => NULL,
    'is_active' => true,
  ),
  374 => 
  array (
    'name' => 'Midazolam',
    'code' => NULL,
    'is_active' => true,
  ),
  375 => 
  array (
    'name' => 'Milrinone',
    'code' => NULL,
    'is_active' => true,
  ),
  376 => 
  array (
    'name' => 'Minocycline',
    'code' => NULL,
    'is_active' => true,
  ),
  377 => 
  array (
    'name' => 'Mirtazapin',
    'code' => NULL,
    'is_active' => true,
  ),
  378 => 
  array (
    'name' => 'Misoprostol',
    'code' => NULL,
    'is_active' => true,
  ),
  379 => 
  array (
    'name' => 'mitomicin',
    'code' => NULL,
    'is_active' => true,
  ),
  380 => 
  array (
    'name' => 'Moclobemide',
    'code' => NULL,
    'is_active' => true,
  ),
  381 => 
  array (
    'name' => 'Mometasone furoate',
    'code' => NULL,
    'is_active' => true,
  ),
  382 => 
  array (
    'name' => 'Morphine',
    'code' => NULL,
    'is_active' => true,
  ),
  383 => 
  array (
    'name' => 'Moxifloxacin',
    'code' => NULL,
    'is_active' => true,
  ),
  384 => 
  array (
    'name' => 'Mupirocin',
    'code' => NULL,
    'is_active' => true,
  ),
  385 => 
  array (
    'name' => 'mycofenoleat',
    'code' => NULL,
    'is_active' => true,
  ),
  386 => 
  array (
    'name' => 'N-acetylcysteine = Acetylcysteine',
    'code' => NULL,
    'is_active' => true,
  ),
  387 => 
  array (
    'name' => 'Nadroparin calcium',
    'code' => NULL,
    'is_active' => true,
  ),
  388 => 
  array (
    'name' => 'Naftidrofuryl',
    'code' => NULL,
    'is_active' => true,
  ),
  389 => 
  array (
    'name' => 'Naloxone',
    'code' => NULL,
    'is_active' => true,
  ),
  390 => 
  array (
    'name' => 'Naltrexone hydrochloride',
    'code' => NULL,
    'is_active' => true,
  ),
  391 => 
  array (
    'name' => 'Nandrolone',
    'code' => NULL,
    'is_active' => true,
  ),
  392 => 
  array (
    'name' => 'Naproxen',
    'code' => NULL,
    'is_active' => true,
  ),
  393 => 
  array (
    'name' => 'Natrium Diklofenak',
    'code' => NULL,
    'is_active' => true,
  ),
  394 => 
  array (
    'name' => 'Neomycin',
    'code' => NULL,
    'is_active' => true,
  ),
  395 => 
  array (
    'name' => 'Neostigmine',
    'code' => NULL,
    'is_active' => true,
  ),
  396 => 
  array (
    'name' => 'Netilmicin',
    'code' => NULL,
    'is_active' => true,
  ),
  397 => 
  array (
    'name' => 'Niacin = Nicotinic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  398 => 
  array (
    'name' => 'Niacinamide = Nicotinamide',
    'code' => NULL,
    'is_active' => true,
  ),
  399 => 
  array (
    'name' => 'Nicardipine',
    'code' => NULL,
    'is_active' => true,
  ),
  400 => 
  array (
    'name' => 'Nicergoline',
    'code' => NULL,
    'is_active' => true,
  ),
  401 => 
  array (
    'name' => 'Nifedipine',
    'code' => NULL,
    'is_active' => true,
  ),
  402 => 
  array (
    'name' => 'Nifuroxazide',
    'code' => NULL,
    'is_active' => true,
  ),
  403 => 
  array (
    'name' => 'Nimesulide',
    'code' => NULL,
    'is_active' => true,
  ),
  404 => 
  array (
    'name' => 'Nimodipine',
    'code' => NULL,
    'is_active' => true,
  ),
  405 => 
  array (
    'name' => 'Nitrazepam',
    'code' => NULL,
    'is_active' => true,
  ),
  406 => 
  array (
    'name' => 'Nitroglyserin = Glyseryl trinitrate',
    'code' => NULL,
    'is_active' => true,
  ),
  407 => 
  array (
    'name' => 'Nomegestrol acetate',
    'code' => NULL,
    'is_active' => true,
  ),
  408 => 
  array (
    'name' => 'Norepinephrine',
    'code' => NULL,
    'is_active' => true,
  ),
  409 => 
  array (
    'name' => 'Norethisterone',
    'code' => NULL,
    'is_active' => true,
  ),
  410 => 
  array (
    'name' => 'Noscapine',
    'code' => NULL,
    'is_active' => true,
  ),
  411 => 
  array (
    'name' => 'Nystatin',
    'code' => NULL,
    'is_active' => true,
  ),
  412 => 
  array (
    'name' => 'Octreotide',
    'code' => NULL,
    'is_active' => true,
  ),
  413 => 
  array (
    'name' => 'Ofloxacin',
    'code' => NULL,
    'is_active' => true,
  ),
  414 => 
  array (
    'name' => 'Omeprazole',
    'code' => NULL,
    'is_active' => true,
  ),
  415 => 
  array (
    'name' => 'Ondansetron',
    'code' => NULL,
    'is_active' => true,
  ),
  416 => 
  array (
    'name' => 'Orciprenaline',
    'code' => NULL,
    'is_active' => true,
  ),
  417 => 
  array (
    'name' => 'Ornithine',
    'code' => NULL,
    'is_active' => true,
  ),
  418 => 
  array (
    'name' => 'Otilonium bromide',
    'code' => NULL,
    'is_active' => true,
  ),
  419 => 
  array (
    'name' => 'Oxaliplatin',
    'code' => NULL,
    'is_active' => true,
  ),
  420 => 
  array (
    'name' => 'Oxomemazine',
    'code' => NULL,
    'is_active' => true,
  ),
  421 => 
  array (
    'name' => 'Oxybenzone',
    'code' => NULL,
    'is_active' => true,
  ),
  422 => 
  array (
    'name' => 'Oxytetracycline',
    'code' => NULL,
    'is_active' => true,
  ),
  423 => 
  array (
    'name' => 'Oxytocin',
    'code' => NULL,
    'is_active' => true,
  ),
  424 => 
  array (
    'name' => 'Paclitaxel',
    'code' => NULL,
    'is_active' => true,
  ),
  425 => 
  array (
    'name' => 'Palonosetron',
    'code' => NULL,
    'is_active' => true,
  ),
  426 => 
  array (
    'name' => 'Pancreatin',
    'code' => NULL,
    'is_active' => true,
  ),
  427 => 
  array (
    'name' => 'Pancuronium bromida',
    'code' => NULL,
    'is_active' => true,
  ),
  428 => 
  array (
    'name' => 'Pantoprazole',
    'code' => NULL,
    'is_active' => true,
  ),
  429 => 
  array (
    'name' => 'Papaverin',
    'code' => NULL,
    'is_active' => true,
  ),
  430 => 
  array (
    'name' => 'Paracetamol',
    'code' => NULL,
    'is_active' => true,
  ),
  431 => 
  array (
    'name' => 'Paraffin',
    'code' => NULL,
    'is_active' => true,
  ),
  432 => 
  array (
    'name' => 'Parecoxib',
    'code' => NULL,
    'is_active' => true,
  ),
  433 => 
  array (
    'name' => 'Parenteral Fat Emulsion',
    'code' => NULL,
    'is_active' => true,
  ),
  434 => 
  array (
    'name' => 'Paromomycin',
    'code' => NULL,
    'is_active' => true,
  ),
  435 => 
  array (
    'name' => 'Pectin',
    'code' => NULL,
    'is_active' => true,
  ),
  436 => 
  array (
    'name' => 'Pefloxacin',
    'code' => NULL,
    'is_active' => true,
  ),
  437 => 
  array (
    'name' => 'Pemirolast',
    'code' => NULL,
    'is_active' => true,
  ),
  438 => 
  array (
    'name' => 'Pentoxifylline',
    'code' => NULL,
    'is_active' => true,
  ),
  439 => 
  array (
    'name' => 'Perindopril',
    'code' => NULL,
    'is_active' => true,
  ),
  440 => 
  array (
    'name' => 'Phenazopyridine',
    'code' => NULL,
    'is_active' => true,
  ),
  441 => 
  array (
    'name' => 'Phenobarbital = Phenobarbitone',
    'code' => NULL,
    'is_active' => true,
  ),
  442 => 
  array (
    'name' => 'Phenolphthalein',
    'code' => NULL,
    'is_active' => true,
  ),
  443 => 
  array (
    'name' => 'Phenyl-propyl-ethylamine',
    'code' => NULL,
    'is_active' => true,
  ),
  444 => 
  array (
    'name' => 'Phenylpropanolamine',
    'code' => NULL,
    'is_active' => true,
  ),
  445 => 
  array (
    'name' => 'Phenyltoloxamine',
    'code' => NULL,
    'is_active' => true,
  ),
  446 => 
  array (
    'name' => 'Phenytoin',
    'code' => NULL,
    'is_active' => true,
  ),
  447 => 
  array (
    'name' => 'Phosphatidyl serine',
    'code' => NULL,
    'is_active' => true,
  ),
  448 => 
  array (
    'name' => 'Phyllanthus niruri',
    'code' => NULL,
    'is_active' => true,
  ),
  449 => 
  array (
    'name' => 'Phytomenadion',
    'code' => NULL,
    'is_active' => true,
  ),
  450 => 
  array (
    'name' => 'Pipemidic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  451 => 
  array (
    'name' => 'Piperacillin',
    'code' => NULL,
    'is_active' => true,
  ),
  452 => 
  array (
    'name' => 'Piracetam',
    'code' => NULL,
    'is_active' => true,
  ),
  453 => 
  array (
    'name' => 'Pirazinamide',
    'code' => NULL,
    'is_active' => true,
  ),
  454 => 
  array (
    'name' => 'Piroxicam',
    'code' => NULL,
    'is_active' => true,
  ),
  455 => 
  array (
    'name' => 'Pizotifen',
    'code' => NULL,
    'is_active' => true,
  ),
  456 => 
  array (
    'name' => 'Policresulen',
    'code' => NULL,
    'is_active' => true,
  ),
  457 => 
  array (
    'name' => 'Polymigel',
    'code' => NULL,
    'is_active' => true,
  ),
  458 => 
  array (
    'name' => 'Potassium aspartate',
    'code' => NULL,
    'is_active' => true,
  ),
  459 => 
  array (
    'name' => 'Povidone-iodine',
    'code' => NULL,
    'is_active' => true,
  ),
  460 => 
  array (
    'name' => 'Pramipexole',
    'code' => NULL,
    'is_active' => true,
  ),
  461 => 
  array (
    'name' => 'Pramiverine',
    'code' => NULL,
    'is_active' => true,
  ),
  462 => 
  array (
    'name' => 'Pravastatin',
    'code' => NULL,
    'is_active' => true,
  ),
  463 => 
  array (
    'name' => 'Prednisone',
    'code' => NULL,
    'is_active' => true,
  ),
  464 => 
  array (
    'name' => 'Pregabalin',
    'code' => NULL,
    'is_active' => true,
  ),
  465 => 
  array (
    'name' => 'Prilocaine',
    'code' => NULL,
    'is_active' => true,
  ),
  466 => 
  array (
    'name' => 'procaine benzylpenicillin = Procaine penicillin',
    'code' => NULL,
    'is_active' => true,
  ),
  467 => 
  array (
    'name' => 'Procaterol',
    'code' => NULL,
    'is_active' => true,
  ),
  468 => 
  array (
    'name' => 'Propofol',
    'code' => NULL,
    'is_active' => true,
  ),
  469 => 
  array (
    'name' => 'Propolis extract',
    'code' => NULL,
    'is_active' => true,
  ),
  470 => 
  array (
    'name' => 'Propranolol',
    'code' => NULL,
    'is_active' => true,
  ),
  471 => 
  array (
    'name' => 'Propylthiouracil',
    'code' => NULL,
    'is_active' => true,
  ),
  472 => 
  array (
    'name' => 'Protease',
    'code' => NULL,
    'is_active' => true,
  ),
  473 => 
  array (
    'name' => 'Pseudoephedrine',
    'code' => NULL,
    'is_active' => true,
  ),
  474 => 
  array (
    'name' => 'Psyllium = Ispaghula',
    'code' => NULL,
    'is_active' => true,
  ),
  475 => 
  array (
    'name' => 'Pyridostigmine bromide',
    'code' => NULL,
    'is_active' => true,
  ),
  476 => 
  array (
    'name' => 'Pyridoxine',
    'code' => NULL,
    'is_active' => true,
  ),
  477 => 
  array (
    'name' => 'Pyritinol',
    'code' => NULL,
    'is_active' => true,
  ),
  478 => 
  array (
    'name' => 'Quetiapine',
    'code' => NULL,
    'is_active' => true,
  ),
  479 => 
  array (
    'name' => 'Quinine',
    'code' => NULL,
    'is_active' => true,
  ),
  480 => 
  array (
    'name' => 'Rabeprazole',
    'code' => NULL,
    'is_active' => true,
  ),
  481 => 
  array (
    'name' => 'Ramipril',
    'code' => NULL,
    'is_active' => true,
  ),
  482 => 
  array (
    'name' => 'Ranitidin',
    'code' => NULL,
    'is_active' => true,
  ),
  483 => 
  array (
    'name' => 'Retinoic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  484 => 
  array (
    'name' => 'Risedronate sodium = Risodronic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  485 => 
  array (
    'name' => 'Risperidone',
    'code' => NULL,
    'is_active' => true,
  ),
  486 => 
  array (
    'name' => 'Rituximab',
    'code' => NULL,
    'is_active' => true,
  ),
  487 => 
  array (
    'name' => 'Rivastigmine',
    'code' => NULL,
    'is_active' => true,
  ),
  488 => 
  array (
    'name' => 'Rocuronium bromide',
    'code' => NULL,
    'is_active' => true,
  ),
  489 => 
  array (
    'name' => 'Ropivacaine',
    'code' => NULL,
    'is_active' => true,
  ),
  490 => 
  array (
    'name' => 'Rosuvastatin',
    'code' => NULL,
    'is_active' => true,
  ),
  491 => 
  array (
    'name' => 'Roxythromycin',
    'code' => NULL,
    'is_active' => true,
  ),
  492 => 
  array (
    'name' => 'Salbutamol',
    'code' => NULL,
    'is_active' => true,
  ),
  493 => 
  array (
    'name' => 'Salmeterol',
    'code' => NULL,
    'is_active' => true,
  ),
  494 => 
  array (
    'name' => 'Sanactase',
    'code' => NULL,
    'is_active' => true,
  ),
  495 => 
  array (
    'name' => 'Schizandrae fructus extr',
    'code' => NULL,
    'is_active' => true,
  ),
  496 => 
  array (
    'name' => 'Selenium',
    'code' => NULL,
    'is_active' => true,
  ),
  497 => 
  array (
    'name' => 'Serrapeptase = Serratiopeptidase',
    'code' => NULL,
    'is_active' => true,
  ),
  498 => 
  array (
    'name' => 'Sertraline',
    'code' => NULL,
    'is_active' => true,
  ),
  499 => 
  array (
    'name' => 'Sevoflurane',
    'code' => NULL,
    'is_active' => true,
  ),
  500 => 
  array (
    'name' => 'Siklofosfamid',
    'code' => NULL,
    'is_active' => true,
  ),
  501 => 
  array (
    'name' => 'Siklosporin',
    'code' => NULL,
    'is_active' => true,
  ),
  502 => 
  array (
    'name' => 'Silicon Emulsion SE-2',
    'code' => NULL,
    'is_active' => true,
  ),
  503 => 
  array (
    'name' => 'Silymarin',
    'code' => NULL,
    'is_active' => true,
  ),
  504 => 
  array (
    'name' => 'Simethicone',
    'code' => NULL,
    'is_active' => true,
  ),
  505 => 
  array (
    'name' => 'Simvastatin',
    'code' => NULL,
    'is_active' => true,
  ),
  506 => 
  array (
    'name' => 'Sodium hyaluronete',
    'code' => NULL,
    'is_active' => true,
  ),
  507 => 
  array (
    'name' => 'Sodium picosulfate',
    'code' => NULL,
    'is_active' => true,
  ),
  508 => 
  array (
    'name' => 'Solifenacin',
    'code' => NULL,
    'is_active' => true,
  ),
  509 => 
  array (
    'name' => 'Sorafenib',
    'code' => NULL,
    'is_active' => true,
  ),
  510 => 
  array (
    'name' => 'Sorbitol',
    'code' => NULL,
    'is_active' => true,
  ),
  511 => 
  array (
    'name' => 'Sparfloxacin',
    'code' => NULL,
    'is_active' => true,
  ),
  512 => 
  array (
    'name' => 'Spiramycin',
    'code' => NULL,
    'is_active' => true,
  ),
  513 => 
  array (
    'name' => 'Spironolactone',
    'code' => NULL,
    'is_active' => true,
  ),
  514 => 
  array (
    'name' => 'Streptokinase',
    'code' => NULL,
    'is_active' => true,
  ),
  515 => 
  array (
    'name' => 'Streptomycin',
    'code' => NULL,
    'is_active' => true,
  ),
  516 => 
  array (
    'name' => 'Succinylcholine chloride = Suxamethonium chloride',
    'code' => NULL,
    'is_active' => true,
  ),
  517 => 
  array (
    'name' => 'Sucralfate',
    'code' => NULL,
    'is_active' => true,
  ),
  518 => 
  array (
    'name' => 'Sufentanil',
    'code' => NULL,
    'is_active' => true,
  ),
  519 => 
  array (
    'name' => 'Sulbactam',
    'code' => NULL,
    'is_active' => true,
  ),
  520 => 
  array (
    'name' => 'Sulbenicillin',
    'code' => NULL,
    'is_active' => true,
  ),
  521 => 
  array (
    'name' => 'Sulbutiamine',
    'code' => NULL,
    'is_active' => true,
  ),
  522 => 
  array (
    'name' => 'Sulfadiazine',
    'code' => NULL,
    'is_active' => true,
  ),
  523 => 
  array (
    'name' => 'Sulfasalazine',
    'code' => NULL,
    'is_active' => true,
  ),
  524 => 
  array (
    'name' => 'Sultamicillin : Ampicillin, Sulbactam',
    'code' => NULL,
    'is_active' => true,
  ),
  525 => 
  array (
    'name' => 'Tamoxifen',
    'code' => NULL,
    'is_active' => true,
  ),
  526 => 
  array (
    'name' => 'Tamsulosin',
    'code' => NULL,
    'is_active' => true,
  ),
  527 => 
  array (
    'name' => 'Tazobactam',
    'code' => NULL,
    'is_active' => true,
  ),
  528 => 
  array (
    'name' => 'Telmisartan',
    'code' => NULL,
    'is_active' => true,
  ),
  529 => 
  array (
    'name' => 'Tenoxicam',
    'code' => NULL,
    'is_active' => true,
  ),
  530 => 
  array (
    'name' => 'Teprenone',
    'code' => NULL,
    'is_active' => true,
  ),
  531 => 
  array (
    'name' => 'Terazosin',
    'code' => NULL,
    'is_active' => true,
  ),
  532 => 
  array (
    'name' => 'Terbinafine',
    'code' => NULL,
    'is_active' => true,
  ),
  533 => 
  array (
    'name' => 'Terbutaline',
    'code' => NULL,
    'is_active' => true,
  ),
  534 => 
  array (
    'name' => 'Terfenadine',
    'code' => NULL,
    'is_active' => true,
  ),
  535 => 
  array (
    'name' => 'test',
    'code' => NULL,
    'is_active' => true,
  ),
  536 => 
  array (
    'name' => 'Testosterone',
    'code' => NULL,
    'is_active' => true,
  ),
  537 => 
  array (
    'name' => 'Tetracycline',
    'code' => NULL,
    'is_active' => true,
  ),
  538 => 
  array (
    'name' => 'Theophylline',
    'code' => NULL,
    'is_active' => true,
  ),
  539 => 
  array (
    'name' => 'Thiabutazide = Butizide',
    'code' => NULL,
    'is_active' => true,
  ),
  540 => 
  array (
    'name' => 'Thiamphenicol',
    'code' => NULL,
    'is_active' => true,
  ),
  541 => 
  array (
    'name' => 'Thiopental sodium',
    'code' => NULL,
    'is_active' => true,
  ),
  542 => 
  array (
    'name' => 'Ticlopidine',
    'code' => NULL,
    'is_active' => true,
  ),
  543 => 
  array (
    'name' => 'Timepidium bromide',
    'code' => NULL,
    'is_active' => true,
  ),
  544 => 
  array (
    'name' => 'Tinoridine',
    'code' => NULL,
    'is_active' => true,
  ),
  545 => 
  array (
    'name' => 'Tioconazole',
    'code' => NULL,
    'is_active' => true,
  ),
  546 => 
  array (
    'name' => 'Tipepidine',
    'code' => NULL,
    'is_active' => true,
  ),
  547 => 
  array (
    'name' => 'Titanium dioxide',
    'code' => NULL,
    'is_active' => true,
  ),
  548 => 
  array (
    'name' => 'Tizanidine',
    'code' => NULL,
    'is_active' => true,
  ),
  549 => 
  array (
    'name' => 'Topiramate',
    'code' => NULL,
    'is_active' => true,
  ),
  550 => 
  array (
    'name' => 'Tramadol',
    'code' => NULL,
    'is_active' => true,
  ),
  551 => 
  array (
    'name' => 'Tranexamic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  552 => 
  array (
    'name' => 'Trastuzumab',
    'code' => NULL,
    'is_active' => true,
  ),
  553 => 
  array (
    'name' => 'Tretinoin',
    'code' => NULL,
    'is_active' => true,
  ),
  554 => 
  array (
    'name' => 'Triamcinolone',
    'code' => NULL,
    'is_active' => true,
  ),
  555 => 
  array (
    'name' => 'Triazolam',
    'code' => NULL,
    'is_active' => true,
  ),
  556 => 
  array (
    'name' => 'Triclosan',
    'code' => NULL,
    'is_active' => true,
  ),
  557 => 
  array (
    'name' => 'Trifluoperazine',
    'code' => NULL,
    'is_active' => true,
  ),
  558 => 
  array (
    'name' => 'Trimetazidine',
    'code' => NULL,
    'is_active' => true,
  ),
  559 => 
  array (
    'name' => 'Tripolidine',
    'code' => NULL,
    'is_active' => true,
  ),
  560 => 
  array (
    'name' => 'Tuna Oil, Vit A',
    'code' => NULL,
    'is_active' => true,
  ),
  561 => 
  array (
    'name' => 'Urea',
    'code' => NULL,
    'is_active' => true,
  ),
  562 => 
  array (
    'name' => 'Urokinase',
    'code' => NULL,
    'is_active' => true,
  ),
  563 => 
  array (
    'name' => 'Ursodeoxycholic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  564 => 
  array (
    'name' => 'Valgansiklovir',
    'code' => NULL,
    'is_active' => true,
  ),
  565 => 
  array (
    'name' => 'Valproic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  566 => 
  array (
    'name' => 'Valsartan',
    'code' => NULL,
    'is_active' => true,
  ),
  567 => 
  array (
    'name' => 'Vancomycin',
    'code' => NULL,
    'is_active' => true,
  ),
  568 => 
  array (
    'name' => 'Vecuronium bromide',
    'code' => NULL,
    'is_active' => true,
  ),
  569 => 
  array (
    'name' => 'Verapamil',
    'code' => NULL,
    'is_active' => true,
  ),
  570 => 
  array (
    'name' => 'Vildagliptin',
    'code' => NULL,
    'is_active' => true,
  ),
  571 => 
  array (
    'name' => 'Vinblastin',
    'code' => NULL,
    'is_active' => true,
  ),
  572 => 
  array (
    'name' => 'Vincristin',
    'code' => NULL,
    'is_active' => true,
  ),
  573 => 
  array (
    'name' => 'Vitamin B1 = Thiamine',
    'code' => NULL,
    'is_active' => true,
  ),
  574 => 
  array (
    'name' => 'Vitamin B12 = Cyanocobalamin',
    'code' => NULL,
    'is_active' => true,
  ),
  575 => 
  array (
    'name' => 'Vitamin B2 = Riboflavin',
    'code' => NULL,
    'is_active' => true,
  ),
  576 => 
  array (
    'name' => 'Vitamin B6 = Pyridoxine',
    'code' => NULL,
    'is_active' => true,
  ),
  577 => 
  array (
    'name' => 'Vitamin C = Ascorbic acid',
    'code' => NULL,
    'is_active' => true,
  ),
  578 => 
  array (
    'name' => 'Vitamin E',
    'code' => NULL,
    'is_active' => true,
  ),
  579 => 
  array (
    'name' => 'Vitamin H',
    'code' => NULL,
    'is_active' => true,
  ),
  580 => 
  array (
    'name' => 'warfarin',
    'code' => NULL,
    'is_active' => true,
  ),
  581 => 
  array (
    'name' => 'Zinc',
    'code' => NULL,
    'is_active' => true,
  ),
  582 => 
  array (
    'name' => 'Zinc Picolinate',
    'code' => NULL,
    'is_active' => true,
  ),
  583 => 
  array (
    'name' => 'Zoledronic Acid',
    'code' => NULL,
    'is_active' => true,
  ),
  584 => 
  array (
    'name' => 'Zolpidem',
    'code' => NULL,
    'is_active' => true,
  ),
);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('active_ingredients')->insert(array_map(static fn ($row) => $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }
    }
}