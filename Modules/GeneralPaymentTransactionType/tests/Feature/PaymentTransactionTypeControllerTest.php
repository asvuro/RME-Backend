<?php

namespace Modules\GeneralPaymentTransactionType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPaymentTransactionType\Models\PaymentTransactionType;
use Tests\TestCase;

class PaymentTransactionTypeControllerTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }
    private function actingUser(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_it_lists_payment_transaction_type(): void
    {
        $this->actingUser();
        PaymentTransactionType::factory()->count(3)->create();

        $this->getJson('/api/v1/payment-transaction-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_payment_transaction_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/payment-transaction-types', ['name' => 'Contoh Jenistransaksipembayarantagihan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenistransaksipembayarantagihan');

        $this->assertDatabaseHas('payment_transaction_types', ['name' => 'Contoh Jenistransaksipembayarantagihan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PaymentTransactionType::factory()->create(['name' => 'Contoh Jenistransaksipembayarantagihan']);

        $this->postJson('/api/v1/payment-transaction-types', ['name' => 'Contoh Jenistransaksipembayarantagihan'])->assertStatus(422);
    }

    public function test_it_deletes_payment_transaction_type(): void
    {
        $this->actingUser();
        $paymentTransactionType = PaymentTransactionType::factory()->create();

        $this->deleteJson("/api/v1/payment-transaction-types/{$paymentTransactionType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('payment_transaction_types', ['id' => $paymentTransactionType->id]);
    }
}