<?php

namespace Modules\AuditRequestLog\Tests\Feature;

use App\Modules\Contracts\HospitalConfig;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\AuditRequestLog\Models\RequestLog;
use Tests\TestCase;

/**
 * Port semangat logs.bridge_log simgos2: setiap request API masuk tercatat
 * (method/url/status/durasi/user/ip), payload sensitif direduksi.
 */
class RequestLogApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_request_api_tercatat_dengan_status_dan_user(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $this->getJson('/api/v1/request-logs')->assertOk();

        $row = RequestLog::query()->firstOrFail();
        $this->assertSame('GET', $row->method);
        $this->assertSame(200, (int) $row->status);
        $this->assertSame($this->admin->id, (int) $row->user_id);
        $this->assertNotNull($row->ip);
        $this->assertNotNull($row->duration_ms);
    }

    public function test_payload_post_tersimpan_tanpa_field_sensitif(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        // Endpoint validasi akan menolak 422 — jejak tetap harus tercipta.
        $this->postJson('/api/v1/visits', [
            'password' => 'rahasia',
            'registration_id' => 999999999,
        ]);

        $row = RequestLog::query()
            ->where('method', 'POST')
            ->firstOrFail();
        $this->assertSame(422, (int) $row->status);
        $this->assertArrayHasKey('registration_id', $row->payload);
        $this->assertArrayNotHasKey('password', $row->payload);
    }

    public function test_path_bukan_api_tidak_dicatat(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $this->getJson('/up');

        $this->assertSame(0, \Modules\AuditRequestLog\Models\RequestLog::count());
    }

    public function test_config_mati_maka_tidak_mencatat(): void
    {
        app(HospitalConfig::class)->set('audit.request_log_enabled', false, 'bool');
        $this->actingAs($this->admin, 'sanctum');

        $this->getJson('/api/v1/request-logs')->assertOk();

        $this->assertSame(0, \Modules\AuditRequestLog\Models\RequestLog::count());
    }

    public function test_index_hanya_untuk_admin(): void
    {
        $petugas = User::factory()->create();
        $this->actingAs($petugas, 'sanctum');

        $this->getJson('/api/v1/request-logs')->assertForbidden();

        $this->app['auth']->guard('sanctum')->forgetUser();
        $this->getJson('/api/v1/request-logs')->assertUnauthorized();
    }
}
