<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\BpjsPCare\Models\Kunjungan;
use Modules\BpjsPCare\Models\Pendaftaran;
use Modules\GeneralDoctor\Models\Doctor;
use Modules\GeneralNurse\Models\Nurse;
use Modules\GeneralStaffMember\Models\StaffMember;
use Modules\MedicalRecordAbdomenExamination\Models\AbdomenExamination;
use Modules\MedicalRecordChestExamination\Models\ChestExamination;
use Modules\MedicalRecordEkgExamination\Models\EkgExamination;
use Modules\MedicalRecordEyeExamination\Models\EyeExamination;
use Modules\MedicalRecordHeadExamination\Models\HeadExamination;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Data skala besar untuk load test (k6), bukan untuk lingkungan produksi.
 * Jalankan: php artisan db:seed --class=Database\\Seeders\\LoadTestSeeder
 */
class LoadTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        $user = User::firstOrCreate(
            ['username' => 'loadtest'],
            [
                'name' => 'Load Test User',
                'email' => 'loadtest@example.test',
                'password' => bcrypt('loadtest-password'),
                'is_locked' => false,
                'is_active' => true,
            ]
        );
        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        $this->command->info('User load test: username=loadtest password=loadtest-password');

        $visitCount = (int) (env('LOAD_TEST_VISITS', 5000));

        $this->command->info("Membuat {$visitCount} Visit (rantai Patient+Registration otomatis via factory)...");
        $visitIds = [];
        foreach (array_chunk(range(1, $visitCount), 200) as $chunk) {
            $visits = Visit::factory()->count(count($chunk))->create();
            $visitIds = array_merge($visitIds, $visits->pluck('id')->all());
        }

        $medicalRecordModels = [
            AbdomenExamination::class,
            EyeExamination::class,
            EkgExamination::class,
            ChestExamination::class,
            HeadExamination::class,
        ];

        $sampledVisitIds = collect($visitIds)->shuffle()->take((int) ($visitCount * 0.4));
        $this->command->info('Mengisi ' . $sampledVisitIds->count() . ' visit dengan rekam medis (5 modul contoh)...');
        foreach ($sampledVisitIds->chunk(200) as $chunk) {
            foreach ($medicalRecordModels as $modelClass) {
                foreach ($chunk as $visitId) {
                    $modelClass::factory()->create(['visit_id' => $visitId]);
                }
            }
        }

        $this->command->info("Membuat {$visitCount} pasangan Pendaftaran+Kunjungan BpjsPCare...");
        foreach (array_chunk(range(1, $visitCount), 200) as $chunk) {
            foreach ($chunk as $_) {
                $pendaftaran = Pendaftaran::factory()->create();
                Kunjungan::factory()->create(['pendaftaran_id' => $pendaftaran->id]);
            }
        }

        $this->command->info('Membuat 200 Doctor/Nurse/StaffMember...');
        Doctor::factory()->count(200)->create();
        Nurse::factory()->count(200)->create();
        StaffMember::factory()->count(200)->create();

        $this->command->info('Selesai.');
    }
}
