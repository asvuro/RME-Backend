<?php

namespace Modules\GeneralInvoiceType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralInvoiceType\Models\InvoiceType;
use Tests\TestCase;

class InvoiceTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_invoice_type(): void
    {
        $this->actingUser();
        InvoiceType::factory()->count(3)->create();

        $this->getJson('/api/v1/invoice-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_invoice_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/invoice-types', ['name' => 'Contoh Jenistagihan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenistagihan');

        $this->assertDatabaseHas('invoice_types', ['name' => 'Contoh Jenistagihan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        InvoiceType::factory()->create(['name' => 'Contoh Jenistagihan']);

        $this->postJson('/api/v1/invoice-types', ['name' => 'Contoh Jenistagihan'])->assertStatus(422);
    }

    public function test_it_deletes_invoice_type(): void
    {
        $this->actingUser();
        $invoiceType = InvoiceType::factory()->create();

        $this->deleteJson("/api/v1/invoice-types/{$invoiceType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('invoice_types', ['id' => $invoiceType->id]);
    }
}