<?php

namespace Modules\AuditActivityLog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditActivityLog\Models\ActivityLog;
use Modules\AuditRequestLog\Models\RequestLog;
use Tests\TestCase;

/**
 * Pengganti partisi tahunan logs.* simgos2: audit:prune memangkas jejak tua.
 */
class AuditPruneCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_jejak_lama_terhapus_dan_yang_baru_tetap(): void
    {
        // created_at di luar fillable: set properti langsung dengan timestamps mati.
        $lama = ActivityLog::make(['user_id' => null, 'action' => 'created', 'object' => 'visits', 'ref' => '1']);
        ActivityLog::withoutTimestamps(fn () => $lama->save());
        ActivityLog::withoutTimestamps(function () use ($lama): void {
            $lama->created_at = now()->subDays(400);
            $lama->save();
        });

        $baru = ActivityLog::query()->create([
            'user_id' => null,
            'action' => 'created',
            'object' => 'visits',
            'ref' => '2',
        ]);

        $requestLama = RequestLog::make(['method' => 'GET', 'url' => 'http://test/api/v1/x']);
        RequestLog::withoutTimestamps(fn () => $requestLama->save());
        RequestLog::withoutTimestamps(function () use ($requestLama): void {
            $requestLama->created_at = now()->subDays(400);
            $requestLama->save();
        });
        RequestLog::query()->create(['method' => 'POST', 'url' => 'http://test/api/v1/y']);

        $this->artisan('audit:prune', ['--days' => 365])->assertSuccessful();

        $this->assertSame(1, ActivityLog::count());
        $this->assertSame($baru->id, (int) ActivityLog::query()->value('id'));
        $this->assertSame(1, RequestLog::count());
    }

    public function test_tanpa_opsi_memakai_default_config(): void
    {
        ActivityLog::query()->create(['user_id' => null, 'action' => 'created', 'object' => 'beds', 'ref' => null]);

        // default audit.prune_days = 365; baris baru tak terhapus.
        $this->artisan('audit:prune')->assertSuccessful();

        $this->assertSame(1, ActivityLog::count());
    }
}
