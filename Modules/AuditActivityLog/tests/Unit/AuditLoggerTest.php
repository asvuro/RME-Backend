<?php

namespace Modules\AuditActivityLog\Tests\Unit;

use App\Modules\Contracts\HospitalConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditActivityLog\Models\ActivityLog;
use Modules\AuditActivityLog\Support\AuditLogger;
use Modules\Auth\Models\User;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Port semangat logs.pengguna_akses_log simgos2: siapa mengubah apa dengan
 * keadaan sebelum/sesudah, field sensitif direduksi.
 */
class AuditLoggerTest extends TestCase
{
    use RefreshDatabase;

    protected AuditLogger $logger;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = app(AuditLogger::class);
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_log_merekam_baris_dengan_aksi_objek_ref(): void
    {
        $this->logger->log(ActivityLog::ACTION_EVENT, 'visit_admission', '12', null, ['visit_number' => 'KJ-1']);

        $row = ActivityLog::query()->firstOrFail();
        $this->assertSame('event', $row->action);
        $this->assertSame('visit_admission', $row->object);
        $this->assertSame('12', $row->ref);
        $this->assertSame($this->user->id, (int) $row->user_id);
        $this->assertNotNull($row->ip);
    }

    public function test_before_dan_after_tersimpan_sebagai_json(): void
    {
        $this->logger->log(ActivityLog::ACTION_UPDATED, 'invoices', '7',
            ['total_amount' => 0], ['total_amount' => 150000]);

        $row = ActivityLog::query()->firstOrFail();
        $this->assertSame(['total_amount' => 0], $row->before);
        $this->assertSame(['total_amount' => 150000], $row->after);
    }

    public function test_field_sensitif_tereduksi(): void
    {
        $this->logger->log(ActivityLog::ACTION_UPDATED, 'users', '3',
            ['password' => 'rahasia', 'name' => 'lama'],
            ['password' => 'baru123', 'token' => 'abc', 'name' => 'baru']);

        $row = ActivityLog::query()->firstOrFail();
        $this->assertSame('[redacted]', $row->before['password']);
        $this->assertSame('[redacted]', $row->after['password']);
        $this->assertSame('[redacted]', $row->after['token']);
        // Field biasa tetap utuh.
        $this->assertSame('baru', $row->after['name']);
    }

    public function test_redaksi_berlaku_pada_array_bersarang(): void
    {
        $this->logger->log(ActivityLog::ACTION_CREATED, 'payloads', null,
            null, ['data' => ['nested' => ['authorization' => 'Bearer xyz']]]);

        $row = ActivityLog::query()->firstOrFail();
        $this->assertSame('[redacted]', $row->after['data']['nested']['authorization']);
    }

    public function test_config_mati_maka_tidak_menulis(): void
    {
        app(HospitalConfig::class)->set('audit.activity_log_enabled', false, 'bool');

        $this->logger->log(ActivityLog::ACTION_DELETED, 'beds', '9', ['x' => 1], null);

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_user_fallback_ke_auth_tanpa_parameter(): void
    {
        Visit::factory()->create(); // model bertrait: create → baris audit.

        $row = ActivityLog::query()->where('object', 'visits')->firstOrFail();
        $this->assertSame(ActivityLog::ACTION_CREATED, $row->action);
        $this->assertSame($this->user->id, (int) $row->user_id);
    }
}
