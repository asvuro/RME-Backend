<?php

namespace Modules\InventoryBloodBag\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\InventoryBloodBag\Models\BloodBag;
use Modules\KemkesBloodType\Models\BloodType;
use Tests\TestCase;

class BloodBagControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_it_creates_blood_bag(): void
    {
        $this->actingUser();
        $bloodType = BloodType::factory()->create();

        $this->postJson('/api/v1/blood-bags', [
            'bag_number' => 'BB-0001',
            'blood_type_id' => $bloodType->id,
            'volume_ml' => 350,
            'collected_at' => now()->subDay()->toDateTimeString(),
            'expires_at' => now()->addDays(30)->toDateTimeString(),
        ])
            ->assertCreated()
            ->assertJsonPath('data.bag_number', 'BB-0001')
            ->assertJsonPath('data.status', BloodBag::STATUS_IN_STOCK);
    }

    public function test_it_lists_blood_bags_filtered_by_status(): void
    {
        $this->actingUser();
        BloodBag::factory()->count(2)->create(['status' => BloodBag::STATUS_IN_STOCK]);
        BloodBag::factory()->create(['status' => BloodBag::STATUS_TRANSFUSED]);

        $this->getJson('/api/v1/blood-bags?status=in_stock')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_bag_number_must_be_unique(): void
    {
        $this->actingUser();
        BloodBag::factory()->create(['bag_number' => 'BB-DUP']);
        $bloodType = BloodType::factory()->create();

        $this->postJson('/api/v1/blood-bags', [
            'bag_number' => 'BB-DUP',
            'blood_type_id' => $bloodType->id,
            'volume_ml' => 350,
            'collected_at' => now()->subDay()->toDateTimeString(),
            'expires_at' => now()->addDays(30)->toDateTimeString(),
        ])->assertStatus(422);
    }

    public function test_delete_rejected_when_bag_not_in_stock(): void
    {
        $this->actingUser();
        $bag = BloodBag::factory()->create(['status' => BloodBag::STATUS_TRANSFUSED]);

        $this->deleteJson("/api/v1/blood-bags/{$bag->id}")
            ->assertStatus(422);
    }
}
