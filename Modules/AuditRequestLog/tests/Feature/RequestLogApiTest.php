<?php

namespace Modules\AuditRequestLog\Tests\Feature;

use App\Modules\Contracts\HospitalConfig;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditRequestLog\Models\RequestLog;
use Modules\Auth\Models\User;
use Tests\TestCase;

/**
 * Port semangat logs.bridge_log simgos2: setiap request API masuk tercatat
 * (method/url/status/durasi/user/ip), payload dibatasi field referensi.
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

        $this->getJson('/api/v1/request-logs?patient_name=Rahasia&token=raw-token')->assertOk();

        $row = RequestLog::query()->firstOrFail();
        $this->assertSame('GET', $row->method);
        $this->assertStringEndsWith('/api/v1/request-logs', $row->url);
        $this->assertStringNotContainsString('?', $row->url);
        $this->assertStringNotContainsString('Rahasia', $row->url);
        $this->assertStringNotContainsString('raw-token', $row->url);
        $this->assertSame(200, (int) $row->status);
        $this->assertSame($this->admin->id, (int) $row->user_id);
        $this->assertNotNull($row->ip);
        $this->assertNotNull($row->duration_ms);
    }

    public function test_payload_post_hanya_mencatat_field_referensi_allowlist(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        // Endpoint validasi akan menolak 422 — jejak tetap harus tercipta.
        $this->postJson('/api/v1/visits', [
            'password' => 'rahasia',
            'registration_id' => 999999999,
            'patient_name' => 'Nama Pasien',
            'clinical_note' => 'Keluhan dan diagnosis mentah',
            'items' => [
                [
                    'service_id' => 123,
                    'result_text' => 'hasil klinis',
                ],
            ],
        ]);

        $row = RequestLog::query()
            ->where('method', 'POST')
            ->firstOrFail();
        $this->assertSame(422, (int) $row->status);
        // 'items' adalah container bersarang -- TIDAK direkam sama sekali
        // (bukan direkursi) supaya klien tak bisa menyuntik referensi palsu
        // lewat penamaan kunci apa pun di kedalaman berapa pun.
        $this->assertSame([
            'registration_id' => 999999999,
        ], $row->payload);
    }

    public function test_nested_containers_are_never_recorded_regardless_of_leaf_key(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $this->postJson('/api/v1/visits', [
            'registration_id' => 1,
            'meta' => ['id' => 'INJECTED-ID'],
            'deeply' => ['nested' => ['payment_ref_id' => 'FORGED']],
        ]);

        $row = RequestLog::query()->where('method', 'POST')->firstOrFail();
        $this->assertSame(['registration_id' => 1], $row->payload);
    }

    public function test_payload_field_count_is_capped(): void
    {
        $this->actingAs($this->admin, 'sanctum');

        $body = [];
        for ($i = 0; $i < 60; $i++) {
            $body["field_{$i}_id"] = str_repeat('B', 5000);
        }

        $this->postJson('/api/v1/visits', $body);

        $row = RequestLog::query()->where('method', 'POST')->firstOrFail();
        $this->assertLessThanOrEqual(20, count($row->payload));
        foreach ($row->payload as $value) {
            $this->assertLessThanOrEqual(255 + 20, strlen($value));
        }
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
