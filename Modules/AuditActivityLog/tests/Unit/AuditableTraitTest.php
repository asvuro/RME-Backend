<?php

namespace Modules\AuditActivityLog\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditActivityLog\Models\ActivityLog;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Trait Auditable pada model mesin state (#7–#11): Visit, Invoice, Bed.
 */
class AuditableTraitTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_mencatat_keadaan_sesudah(): void
    {
        $visit = Visit::factory()->create(['status' => 'active']);

        $row = ActivityLog::query()->where('object', 'visits')->firstOrFail();
        $this->assertSame(ActivityLog::ACTION_CREATED, $row->action);
        $this->assertSame((string) $visit->id, $row->ref);
        $this->assertNull($row->before);
        $this->assertSame('active', $row->after['status']);
    }

    public function test_update_mencatat_diff_sebelum_sesudah(): void
    {
        $visit = Visit::factory()->create(['status' => 'active']);
        ActivityLog::query()->where('object', 'visits')->delete();

        $visit->update(['status' => 'discharged', 'final_outcome' => 'sembuh']);

        $row = ActivityLog::query()->where('object', 'visits')->firstOrFail();
        $this->assertSame(ActivityLog::ACTION_UPDATED, $row->action);
        $this->assertSame('active', $row->before['status']);
        $this->assertSame('discharged', $row->after['status']);
        $this->assertArrayNotHasKey('visit_number', $row->after); // hanya kolom berubah
        $this->assertArrayNotHasKey('updated_at', $row->after);
    }

    public function test_update_tanpa_perubahan_tidak_menulis(): void
    {
        $visit = Visit::factory()->create();

        ActivityLog::query()->delete();
        $visit->update(['status' => $visit->status]);

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_delete_mencatat_keadaan_sebelum(): void
    {
        $visit = Visit::factory()->create(['final_outcome' => 'meninggal']);

        ActivityLog::query()->delete();
        $visit->delete();

        $row = ActivityLog::query()->where('object', 'visits')->firstOrFail();
        $this->assertSame(ActivityLog::ACTION_DELETED, $row->action);
        $this->assertSame('meninggal', $row->before['final_outcome']);
        $this->assertNull($row->after);
    }
}
