<?php

namespace Modules\AplikasiSetting\Tests\Feature;

use App\Modules\Contracts\HospitalConfig;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\RsSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Tests\TestCase;

/**
 * API konfigurasi RS (port REST PropertiConfig simgos2).
 * Baca: semua pengguna terautentikasi. Tulis: hanya role:admin (middleware rute).
 */
class AplikasiSettingApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(RsSettingSeeder::class);
    }

    private function actingUser(?string $role = null): User
    {
        $user = User::factory()->create();
        if ($role !== null) {
            $user->assignRole($role);
        }
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_tamu_ditolak_401(): void
    {
        $this->getJson('/api/v1/settings')->assertUnauthorized();
    }

    public function test_index_mengembalikan_kunci_terseed_dengan_nilai_tercast(): void
    {
        $this->actingUser();

        $response = $this->getJson('/api/v1/settings');

        $response->assertOk();
        $data = $response->json('data');

        // Seeder memuat 59 kunci (9 warisan #7 + 50 port #8).
        $this->assertGreaterThanOrEqual(59, count($data));

        // bool ter-cast benar-benar boolean, json ter-cast array.
        $this->assertTrue($data['billing.lock_on_cashier_close']['value']);
        $this->assertSame(365, $data['pasien.infant_age_limit_days']['value']);
        $this->assertSame([], $data['pharmacy.executive_rooms']['value']);
        $this->assertSame('string', $data['general.hospital_name']['type']);
    }

    public function test_show_mengembalikan_satu_kunci_dan_404_bila_tak_ada(): void
    {
        $this->actingUser();

        $this->getJson('/api/v1/settings/billing.lock_on_cashier_close')
            ->assertOk()
            ->assertJsonFragment(['type' => 'bool']);

        $this->getJson('/api/v1/settings/tidak.ada.kunci')
            ->assertNotFound();
    }

    public function test_admin_dapat_update_dan_cache_terflush(): void
    {
        $this->actingUser('admin');
        $config = app(HospitalConfig::class);

        // Nilai lama true (default seeder) dan sudah ter-cache saat dibaca.
        $this->assertTrue($config->get('billing.lock_on_cashier_close'));

        $this->putJson('/api/v1/settings/billing.lock_on_cashier_close', [
            'value' => false,
            'description' => 'Dimatikan manajemen',
        ])->assertOk()->assertJsonFragment(['value' => false]);

        // Bukti flush cache rememberForever: get() langsung membaca nilai baru.
        $this->assertFalse($config->get('billing.lock_on_cashier_close'));
        $this->assertSame('Dimatikan manajemen', $config->entries()['billing.lock_on_cashier_close']['description']);
    }

    public function test_admin_dapat_membuat_kunci_baru_via_post(): void
    {
        $this->actingUser('admin');

        $this->postJson('/api/v1/settings', [
            'key' => 'cetakan.footer_note',
            'value' => 'Dicetak oleh sistem RME',
            'type' => 'string',
        ])->assertCreated()->assertJsonFragment(['value' => 'Dicetak oleh sistem RME']);

        $this->assertSame(
            'Dicetak oleh sistem RME',
            app(HospitalConfig::class)->get('cetakan.footer_note'),
        );
    }

    public function test_non_admin_ditolak_403_saat_menulis(): void
    {
        $this->actingUser(); // tanpa role

        $this->putJson('/api/v1/settings/billing.allow_final_layanan', ['value' => false])
            ->assertForbidden();
        $this->postJson('/api/v1/settings', ['key' => 'x.y', 'value' => 1])
            ->assertForbidden();
    }

    public function test_validasi_type_dan_format_kunci(): void
    {
        $this->actingUser('admin');

        $this->putJson('/api/v1/settings/billing.allow_final_layanan', [
            'value' => 'ya',
            'type' => 'float',
        ])->assertStatus(422);

        $this->postJson('/api/v1/settings', [
            'key' => 'Kunci Tidak Valid',
            'value' => 1,
        ])->assertStatus(422);
    }

    public function test_seeder_idempoten_dan_tidak_menimpa_override(): void
    {
        $config = app(HospitalConfig::class);
        $config->set('pasien.norm_max_manual', 7777, 'int', 'override deploy');

        // Jalankan ulang seeder: nilai override harus tetap.
        $this->seed(RsSettingSeeder::class);

        $this->assertSame(7777, $config->get('pasien.norm_max_manual'));
    }
}
