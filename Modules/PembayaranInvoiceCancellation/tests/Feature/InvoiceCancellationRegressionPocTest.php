<?php

namespace Modules\PembayaranInvoiceCancellation\Tests\Feature;

use App\Events\InvoiceLocked;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Auth\Models\User;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoiceCancellation\Models\InvoiceCancellation;
use Tests\TestCase;

/**
 * PoC: InvoiceCancellationController::store() bypasses InvoiceService::cancel().
 *
 * The hand-rolled flow updates the invoice inline inside its own transaction
 * and then dispatches App\Events\InvoiceLocked AFTER commit on a freshly
 * fetched model. That drops the canonical lock path (InvoiceService owns lock
 * semantics), widens the commit-to-dispatch window, and leaves duplicate
 * prevention resting only on an application-level exists() check with no DB
 * unique index as backstop.
 */
class InvoiceCancellationRegressionPocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingPetugas(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_invoice_locked_event_carries_committed_state_after_the_cancellation_transaction(): void
    {
        $this->actingPetugas();
        $invoice = Invoice::factory()->create(['status' => 'open', 'is_locked' => false]);

        $observedStates = [];
        Event::listen(InvoiceLocked::class, function (InvoiceLocked $event) use (&$observedStates) {
            $observedStates[] = [
                'invoice_is_locked' => $event->invoice->is_locked,
                'invoice_status' => $event->invoice->status,
                'cancellation_rows' => InvoiceCancellation::query()->where('invoice_id', $event->invoice->id)->count(),
                'audit_listener_will_read_db' => InvoiceLocked::class,
            ];
        });

        $response = $this->postJson('/api/v1/invoice-cancellations', [
            'invoice_id' => $invoice->id,
            'reason' => 'Salah input pasien',
        ]);

        $response->assertCreated();

        // Exactly one canonical event must fire, AFTER the whole cancellation
        // transaction commits -- the audit listener reads the DB, so it must
        // never observe a state where the invoice is locked but the legal
        // cancellation row is not there yet.
        $this->assertCount(1, $observedStates);
        $this->assertTrue($observedStates[0]['invoice_is_locked']);
        $this->assertSame('cancelled', $observedStates[0]['invoice_status']);
        $this->assertSame(1, $observedStates[0]['cancellation_rows'],
            'the audit listener reads the DB after dispatch: cancellation must already be committed');

        $this->assertSame(1, count($observedStates),
            'exactly one InvoiceLocked event must carry the committed state');
    }

    public function test_duplicate_cancellation_has_no_database_level_backstop(): void
    {
        $this->actingPetugas();
        $invoice = Invoice::factory()->create(['status' => 'open', 'is_locked' => false]);

        $this->postJson('/api/v1/invoice-cancellations', [
            'invoice_id' => $invoice->id,
            'reason' => 'first cancel',
        ])->assertCreated();

        // Application gate holds for sequential requests...
        $this->postJson('/api/v1/invoice-cancellations', [
            'invoice_id' => $invoice->id,
            'reason' => 'second cancel bypassing the gate',
        ])->assertStatus(422);
        $this->assertSame(1, InvoiceCancellation::query()->where('invoice_id', $invoice->id)->count());

        // ...but nothing structural enforces it. A writer that skips the
        // locking transaction reintroduces vuln-0017 instantly:
        InvoiceCancellation::query()->create([
            'invoice_id' => $invoice->id,
            'cancelled_at' => now(),
            'cancelled_by' => User::factory()->create()->id,
            'reason' => 'duplicate written without the app-level gate',
        ]);

        $this->assertSame(2, InvoiceCancellation::query()->where('invoice_id', $invoice->id)->count(),
            'no unique index on invoice_cancellations.invoice_id stops a second cancellation row');
    }
}