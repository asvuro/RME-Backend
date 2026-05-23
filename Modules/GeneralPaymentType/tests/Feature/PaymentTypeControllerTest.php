<?php

namespace Modules\GeneralPaymentType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPaymentType\Models\PaymentType;
use Tests\TestCase;

class PaymentTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_payment_type(): void
    {
        $this->actingUser();
        PaymentType::factory()->count(3)->create();

        $this->getJson('/api/v1/payment-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_payment_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/payment-types', ['name' => 'Contoh Jenispembayaran', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispembayaran');

        $this->assertDatabaseHas('payment_types', ['name' => 'Contoh Jenispembayaran']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PaymentType::factory()->create(['name' => 'Contoh Jenispembayaran']);

        $this->postJson('/api/v1/payment-types', ['name' => 'Contoh Jenispembayaran'])->assertStatus(422);
    }

    public function test_it_deletes_payment_type(): void
    {
        $this->actingUser();
        $paymentType = PaymentType::factory()->create();

        $this->deleteJson("/api/v1/payment-types/{$paymentType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('payment_types', ['id' => $paymentType->id]);
    }
}