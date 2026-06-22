<?php

namespace Modules\GeneralPackageItemType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPackageItemType\Models\PackageItemType;
use Tests\TestCase;

class PackageItemTypeControllerTest extends TestCase
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

    public function test_it_lists_package_item_type(): void
    {
        $this->actingUser();
        PackageItemType::factory()->count(3)->create();

        $this->getJson('/api/v1/package-item-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_package_item_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/package-item-types', ['name' => 'Contoh Jenisitempaket', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisitempaket');

        $this->assertDatabaseHas('package_item_types', ['name' => 'Contoh Jenisitempaket']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PackageItemType::factory()->create(['name' => 'Contoh Jenisitempaket']);

        $this->postJson('/api/v1/package-item-types', ['name' => 'Contoh Jenisitempaket'])->assertStatus(422);
    }

    public function test_it_deletes_package_item_type(): void
    {
        $this->actingUser();
        $packageItemType = PackageItemType::factory()->create();

        $this->deleteJson("/api/v1/package-item-types/{$packageItemType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('package_item_types', ['id' => $packageItemType->id]);
    }
}