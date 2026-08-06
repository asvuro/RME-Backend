<?php

namespace Modules\MedicalRecordRetentionSchedule\Tests\Feature;

use App\Modules\Contracts\HospitalConfig;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPatient\Models\Patient;
use Modules\MedicalRecordRetentionSchedule\Models\RetentionSchedule;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Port aturan Permenkes 24/2022: retention_due_at = basis_date + N tahun
 * (HospitalConfig 'retention.years', default 25), lalu status active ->
 * eligible_for_destruction setelah due date lewat. Command `retention:scan`
 * adalah satu-satunya jalur yang mengisi baris (bukan endpoint HTTP, karena
 * modul ini read-only — lihat routes/api.php).
 */
class RetentionScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    protected function actingAsAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_scan_command_creates_schedule_with_correct_due_date_from_discharged_visit(): void
    {
        app(HospitalConfig::class)->set('retention.years', 25, 'int');

        $patient = Patient::factory()->create();
        $registration = Registration::factory()->create(['patient_id' => $patient->id]);
        $dischargedAt = now()->subYears(2);
        Visit::factory()->create([
            'registration_id' => $registration->id,
            'discharged_at' => $dischargedAt,
        ]);

        $this->artisan('retention:scan')->assertSuccessful();

        $schedule = RetentionSchedule::query()->where('registration_id', $registration->id)->firstOrFail();

        $this->assertSame($patient->id, $schedule->patient_id);
        $this->assertSame(25, $schedule->retention_years);
        $this->assertSame(
            $dischargedAt->copy()->addYears(25)->toDateString(),
            $schedule->retention_due_at->toDateString(),
        );
        $this->assertSame(RetentionSchedule::STATUS_ACTIVE, $schedule->status);
    }

    public function test_scan_command_marks_overdue_schedule_as_eligible_for_destruction(): void
    {
        app(HospitalConfig::class)->set('retention.years', 25, 'int');

        $registration = Registration::factory()->create();
        // Kunjungan pulang 30 tahun lalu -> due date 5 tahun lalu, sudah lewat.
        Visit::factory()->create([
            'registration_id' => $registration->id,
            'discharged_at' => now()->subYears(30),
        ]);

        // Registrasi kedua masih baru, belum jatuh tempo.
        $freshRegistration = Registration::factory()->create();
        Visit::factory()->create([
            'registration_id' => $freshRegistration->id,
            'discharged_at' => now()->subYear(),
        ]);

        $this->artisan('retention:scan')->assertSuccessful();

        $overdue = RetentionSchedule::query()->where('registration_id', $registration->id)->firstOrFail();
        $fresh = RetentionSchedule::query()->where('registration_id', $freshRegistration->id)->firstOrFail();

        $this->assertSame(RetentionSchedule::STATUS_ELIGIBLE_FOR_DESTRUCTION, $overdue->status);
        $this->assertSame(RetentionSchedule::STATUS_ACTIVE, $fresh->status);
    }

    public function test_scan_command_is_idempotent_and_does_not_duplicate_rows(): void
    {
        $registration = Registration::factory()->create();
        Visit::factory()->create(['registration_id' => $registration->id, 'discharged_at' => now()->subYears(2)]);

        $this->artisan('retention:scan')->assertSuccessful();
        $this->artisan('retention:scan')->assertSuccessful();

        $this->assertSame(1, RetentionSchedule::query()->where('registration_id', $registration->id)->count());
    }

    public function test_admin_can_list_and_show_retention_schedules(): void
    {
        $this->actingAsAdmin();

        $schedule = RetentionSchedule::factory()->create();

        $listResponse = $this->getJson('/api/v1/retention-schedules');
        $listResponse->assertOk()->assertJsonCount(1, 'data');

        $showResponse = $this->getJson("/api/v1/retention-schedules/{$schedule->id}");
        $showResponse->assertOk()->assertJsonPath('data.id', $schedule->id);
    }

    public function test_non_admin_forbidden_and_guest_unauthorized(): void
    {
        $this->getJson('/api/v1/retention-schedules')->assertStatus(401);

        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        $this->getJson('/api/v1/retention-schedules')->assertStatus(403);
    }
}
