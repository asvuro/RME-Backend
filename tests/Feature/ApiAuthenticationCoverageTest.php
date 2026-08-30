<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Tests\TestCase;

/**
 * Regression test temuan security-review K-1 (2026-08-25): 10 modul ini
 * mendaftarkan apiResource penuh TANPA auth:sanctum sehingga data identitas
 * pasien & SDM RS bisa dibaca/diubah/dihapus anonim. Setiap prefix wajib
 * menolak request anonim dengan 401 pada index maupun store.
 */
class ApiAuthenticationCoverageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prefix rute yang sebelumnya terekspos tanpa autentikasi.
     *
     * @return array<string, string>
     */
    public static function exposedPrefixes(): array
    {
        return [
            'identitas KTP keluarga pasien' => 'patientfamilyidentitycards',
            'keluarga pasien' => 'patientfamilies',
            'kontak keluarga pasien' => 'patientfamilycontacts',
            'staf RS' => 'staff-members',
            'dokter' => 'doctors',
            'perawat' => 'nurses',
            'SMF dokter' => 'doctor-medical-departments',
            'penempatan dokter per ruangan' => 'doctor-ward-assignments',
            'penempatan perawat per ruangan' => 'nurse-ward-assignments',
            'penempatan staf per ruangan' => 'staff-ward-assignments',
        ];
    }

    public function test_anonymous_index_requests_are_rejected_on_previously_exposed_endpoints(): void
    {
        foreach (self::exposedPrefixes() as $label => $prefix) {
            try {
                $this->getJson("/api/v1/{$prefix}")->assertStatus(401);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $this->fail("GET anonim pada {$label} (api/{$prefix}) tidak ditolak 401.");
            }
        }
    }

    public function test_anonymous_store_requests_are_rejected_on_previously_exposed_endpoints(): void
    {
        foreach (self::exposedPrefixes() as $label => $prefix) {
            try {
                $this->postJson("/api/v1/{$prefix}", ['name' => 'Penyusup'])->assertStatus(401);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                $this->fail("POST anonim pada {$label} (api/{$prefix}) tidak ditolak 401.");
            }
        }
    }

    public function test_authenticated_users_still_reach_the_protected_endpoints(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');

        // Cukup satu sampel per kelompok - yang diverifikasi adalah middleware,
        // bukan isi controller (sudah dicover test modul masing-masing).
        $this->getJson('/api/v1/doctors')->assertOk();
        $this->getJson('/api/v1/patientfamilyidentitycards')->assertOk();
    }
}
