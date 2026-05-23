<?php

namespace Modules\AuditActivityLog\Tests\Feature;

use App\Events\InvoiceLocked;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoice\Services\InvoiceService;
use Modules\PendaftaranVisit\Models\Visit;
use Modules\PendaftaranVisit\Services\VisitService;
use Tests\TestCase;

/**
 * Milestone domain #7–#11 → jejak semantik action='event', dan akses baca
 * khusus admin (jejak audit bukan konsumsi umum).
 */
class ActivityLogApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->actingAs($this->admin, 'sanctum');
    }

    public function test_admit_menghasilkan_jejak_mekanis_dan_semantik(): void
    {
        $visit = app(VisitService::class)->admit([
            'registration_id' => \Modules\PendaftaranRegistration\Models\Registration::factory()->create()->id,
        ], $this->admin);

        // Mekanis dari trait Auditable.
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'created',
            'object' => 'visits',
            'ref' => (string) $visit->id,
        ]);

        // Semantik dari DomainEventAuditListener.
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'event',
            'object' => 'visit_admission',
            'ref' => (string) $visit->id,
        ]);
    }

    public function test_lock_invoice_tercatat_sebagai_event(): void
    {
        $invoice = Invoice::factory()->create();

        app(InvoiceService::class)->lock($invoice->id);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'event',
            'object' => 'invoice_lock',
            'ref' => (string) $invoice->id,
        ]);
    }

    public function test_index_hanya_untuk_admin(): void
    {
        ActivityLogSeederHelper::seedRow();

        $this->getJson('/api/v1/activity-logs')->assertOk()->assertJsonCount(1, 'data');

        $petugas = User::factory()->create();
        $this->actingAs($petugas, 'sanctum')
            ->getJson('/api/v1/activity-logs')->assertForbidden();

        $this->app['auth']->guard('sanctum')->forgetUser();
        $this->getJson('/api/v1/activity-logs')->assertUnauthorized();
    }

    public function test_index_bisa_difilter_per_objek(): void
    {
        ActivityLogSeederHelper::seedRow(['object' => 'invoices']);
        ActivityLogSeederHelper::seedRow(['object' => 'beds']);

        $response = $this->getJson('/api/v1/activity-logs?object=beds');

        $response->assertOk()->assertJsonCount(1, 'data');
        $this->assertSame('beds', $response->json('data.0.object'));
    }
}

/**
 * Helper kecil: baris log mentah TANPA memicu trait Auditable
 * (jangan create model bertrait di sini — itu menambah baris lain).
 */
class ActivityLogSeederHelper
{
    public static function seedRow(array $overrides = []): void
    {
        \Modules\AuditActivityLog\Models\ActivityLog::query()->create(array_merge([
            'user_id' => null,
            'action' => 'created',
            'object' => 'visits',
            'ref' => '777',
            'before' => null,
            'after' => ['status' => 'active'],
            'ip' => '127.0.0.1',
        ], $overrides));
    }
}
